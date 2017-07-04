<?php
namespace backend\controllers;

use backend\components\RbacFilter;
use backend\models\LoginForm;
use backend\models\User;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class UserController extends \yii\web\Controller
{
    //用户注册
    public function actionRegister()
    {
        $model = new User(['scenario'=>User::SCENARIO_REG]);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $model->created_at = time();//注册时间
            $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);//密码加密
            $model->save(false);//数据保存，然后根据ID关联角色
            $id = $model->getOldAttribute('id');//获得刚刚保存用户的ID
            $authmanage = \Yii::$app->authManager;
            foreach ($model->roles as $role){//循环分配多个角色
                $ro = $authmanage->getRole($role);//找到每个角色
                $authmanage->assign($ro,$id);//将角色分配给当前ID的用户
            }
            \Yii::$app->session->setFlash('success','注册成功,请登录');
            return $this->redirect(['user/login','username'=>$model->username]);
        }
        return $this->render('register',['model'=>$model]);
    }



    //自动登录测试
    public function actionLogin1(){
        $model = new LoginForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //调用login方法
            if($model->login()){
                \Yii::$app->session->setFlash('success','登录成功');
                return $this->redirect(['user/index']);
            }
        }
        return $this->render('login',['model'=>$model]);
    }





    //用户登录
    public function actionLogin(){
        $model = new User(['scenario'=>User::SCENARIO_LOGIN]);
            if($model->load(\Yii::$app->request->post()) && $model->validate()){
                //查出用户
                $user = User::findOne(['username'=>$model->username]);
                if($user){
                    //比对密码
                    if(\Yii::$app->security->validatePassword($model->password_m,$user->password_hash)){
                        //登录,自动登录
                        //var_dump($model->rememberme);exit;
                        if($model->rememberme){
                            //先生成auth_key
                            $user->auth_key = \Yii::$app->security->generateRandomString() ;
                            \Yii::$app->user->login($user,3600*7*24);
                        }else{
                            \Yii::$app->user->login($user);
                        }
                        //登录设置时间，是否自动登录
                        \Yii::$app->user->login($user,$model->rememberme ? 3600*24*7 : 0);

                        $user->last_login_time = time();//最后登录时间
                        $user->last_login_ip = $_SERVER['REMOTE_ADDR'];//最后登录Ip
                        $user->save(false);

                        echo  '<script>alert("登录成功");</script>';
                        return $this->redirect(['user/index','login_user'=>$user->username]);
                    }else{
                        echo  '<script>alert("用户名或密码错误");</script>';
                    }
                }else{
                    echo  '<script>alert("用户名或密码错误");</script>';
                }
            }
        return $this->render('login',['model'=>$model]);
    }


    //用户注销
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->redirect(['user/login']);
    }

    //用户列表
    public function actionIndex()
    {
        $users = User::find()->all();
        $login_user = \Yii::$app->user->id;
        return $this->render('index',['users'=>$users,'login_user'=>$login_user]);
    }

    //删除
    public function actionDelete($id){
        $user = User::findOne(['id'=>$id])->delete();
        return $this->redirect('index');
    }

    //修改
    public function actionEdit($id){
        $model = User::findOne(['id'=>$id]);
        $model->scenario=User::SCENARIO_EDIT;
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            \Yii::$app->session->setFlash('success','修改成功');
            return $this->redirect(['user/index']);
        }
        //回显角色
        $model->roles = ArrayHelper::map(\Yii::$app->authManager->getRolesByUser($id),'name','name');
        return $this->render('chgpwd',['model'=>$model]);
    }


    //验证码
    public function actions(){
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength' => 4,//验证码最小长度
                'maxLength'=>4,//最大长度
            ],
        ];
    }
//过滤器,围墙
    public function behaviors()
    {
        return [
            'acf'=>[
                'class'=>AccessControl::className(),
                'only'=>['register','login','index','delete','edit'],//该过滤器作用的操作 ，默认是所有操作
                'rules'=>[
                    [//未认证用户允许执行的操作
                        'allow'=>true,//是否允许执行
                        'actions'=>['login','captcha','init','logout'],//指定操作
                        'roles'=>['?'],//角色？表示未认证用户
                    ],
                    [//已认证用户允许执行的操作
                        'allow'=>true,//是否允许执行
                        'actions'=>['login','index','register','init','delete','edit','captcha','logout'],//指定操作
                        'roles'=>['@'],//角色 @表示已认证用户
                    ],
                    [//已认证用户允许执行的操作
                        'allow'=>true,//是否允许执行
                        'actions'=>['login1','index','register','init','delete','login','captcha','logout'],//指定操作
                        'roles'=>['@'],//角色 @表示已认证用户
                    ],
                    //其他都禁止执行
                ]
            ],
        ];
    }
}
