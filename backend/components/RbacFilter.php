<?php
namespace backend\components;
use yii\base\ActionFilter;
use yii\web\HttpException;

class RbacFilter extends ActionFilter{
    public function beforeAction($action){
        $user = \Yii::$app->user;
        if(!$user->can($action->uniqueId)){
            //如果用户未登录，引导用户先登录
            if($user->isGuest){
                return $action->controller->redirect($user->loginUrl);
            }
            throw new HttpException(403,'对不起您没有该权限');
            return false;
        }
        return parent::beforeAction($action);
    }
}