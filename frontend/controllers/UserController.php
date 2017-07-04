<?php

namespace frontend\controllers;

use frontend\models\Member;

header('content:text/html; charset=utf-8');
class UserController extends \yii\web\Controller
{
    public $layout = 'login';//使用login布局文件

    //用户注册功能register
    public function actionRegister(){
        $model = new Member(['scenario'=>Member::SCENARIO_REG]);
        if($model->load(\Yii::$app->request->post()) ){
            if($model->reg()){ //验证成功并保存到数据库
                echo '<script>confirm("注册成功,请返回登录");location.href="login.html";</script>';
                \Yii::$app->session->setFlash('success','注册成功');
                return $this->redirect(['login','model'=>$model]);  //跳转页面
            }
        }
        return $this->render('register',['model'=>$model]);//视图页面
    }



    //用户登录功能
    public function actionLogin(){
        $model = new Member(['scenario'=>Member::SCENARIO_LOGIN]);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->login()){//验证登录 信息并登录
                \Yii::$app->session->setFlash('success','登录成功');
                return $this->redirect(['goods/index']);
            }
        }
        return $this->render('login',['model'=>$model]);
    }

    //用户注销
    public function actionLogout(){
        \Yii::$app->user->logout();
        return $this->redirect(['user/login']);
    }


    //用户注册短信发送
    public function actionSendMsg(){
        //$tel = '18780287692';
        $tel = \Yii::$app->request->post('tel');

        //var_dump($tel);exit;
        if(!preg_match('/^1[34578]\d{9}$/',$tel)){
            echo '手机号格式不正确';exit;
        }
        //发送短信
        $code = rand(1000,9999);
        $result =  \Yii::$app->sms->setNum($tel)->setPara(['code'=>$code])->send();
       // var_dump($result);exit;
        if($result){
            //保存当前验证码 session  mysql  redis  不能保存到cookie
            \Yii::$app->cache->set('tal_',$tel,$code,600);
            echo 'success'.$code;
        }else{
            echo '发送失败';
        }
    }


    //用户中心
    public function actionUser(){
        $this->layout = 'goods';
        return $this->render('user');
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

}
