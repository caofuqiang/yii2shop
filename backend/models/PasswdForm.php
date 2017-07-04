<?php
namespace backend\models;
use yii\base\Model;

class PasswdForm extends Model{
    //定义表单字段
    public $oldPassword;//旧密码
    public $newPassword;//新密码
    public $rePassword;//确认新密码

    public function rules()
    {
        return [
            [['username','oldPassword','newPassword','rePassword'],'required'],
            //旧密码要正确
            ['oldPassword','validatePassword'],
            //新密码和确认新密码要一致
            ['rePassword','compare','compareAttribute'=>'newPassword','message'=>'两次密码必须一致'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'oldPassword'=>'旧密码',
            'newPassword'=>'新密码',
            'rePassword'=>'确认密码',
        ];
    }

    public function validatePassword(){
      //  $account = Username::findOne(['username'=>$this->username]);
        $passwordHash = \Yii::$app->user->identity->password;
        $password = $this->oldPassword;
        if(!\Yii::$app->security->validatePassword($password,$passwordHash)){
            $this->addError('oldPassword','旧密码不正确');
        };
    }
}