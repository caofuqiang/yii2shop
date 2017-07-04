<?php
namespace backend\models;
use yii\base\Model;

class LoginForm extends Model{
    public $username;//用户名
    public $password;//密码
    public $rememberMe;
    public $code;

    public function rules()
    {
        return [
            [['username','password'],'required'],
            //添加自定义验证方法
            ['username','validateUsername'],
            ['code','captcha'],
            ['rememberMe','boolean']
        ];
    }

    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password'=>'密码',
            'rememberMe'=>'记住密码'
        ];
    }

    //自定义验证方法
    public function validateUsername(){
        $username = Username::findOne(['username'=>$this->username]);
        if($username){
            //用户存在 验证密码
//            if($this->password != $account->password){
            if(\Yii::$app->security->validatePassword($this->password,$username->password)){
                //账号秘密正确，登录
                $duration = $this->rememberMe?7*24*3600:0;
                \Yii::$app->user->login($username,$duration);
            }else{
                $this->addError('password','密码不正确');
            }
        }else{
            //账号不存在  添加错误
            $this->addError('username','账号不正确');
        }
    }
}