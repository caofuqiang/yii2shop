<?php
namespace  frontend\controllers;

class AlidayuController extends \yii\web\Controller{

    public function actionSms(){
       $result =  \Yii::$app->sms->setNum(13678029077)->setPara(['code'=>rand(1000,9999)])->send();
       if($result){
           echo 'success';
       }else{
           echo '发送失败';
       }
    }
}