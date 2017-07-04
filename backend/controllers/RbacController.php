<?php

namespace backend\controllers;

use backend\components\RbacFilter;
use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\web\NotFoundHttpException;

class RbacController extends \yii\web\Controller
{
//. 使用RBAC管理用户和权限
        //. 权限增删改查
        //显示列表
        public function actionPermissionIndex(){
            $models = \Yii::$app->authManager->getPermissions();
            return $this->render('permission-index',['models'=>$models]);
        }
        //添加权限
    public function actionAddPermission()
    {
        $model = new PermissionForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->addPermission()){
                \Yii::$app->session->setFlash('success','权限添加成功');
                return $this->redirect(['permission-index']);
            }
        }

        return $this->render('add-permission',['model'=>$model]);
    }
        //修改权限
    public function actionEditPermission($name){
        $permission = \Yii::$app->authManager->getPermission($name);
            if($permission == null){
                throw new NotFoundHttpException('权限不存在');
            }
            $model = new PermissionForm();
           $model->loadData($permission);
             //$model->loadData($permission);
           // var_dump($permission);exit;
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->addPermission()){
                \Yii::$app->session->setFlash('success','权限修改成功');
                return $this->redirect(['permission-index']);
            }
        }

        return $this->render('add-permission',['model'=>$model]);
    }
        //删除权限
    public function actionDelPermission($name){
        $permission = \Yii::$app->authManager->getPermission($name);
        if($permission == null){
            throw new NotFoundHttpException('权限不存在');
        }
        \Yii::$app->authManager->remove($permission);
        \Yii::$app->session->setFlash('success','权限删除成功');
        return $this->redirect(['permission-index']);
    }
// 角色增删改查
//=============================================================================
//创建一个角色
    public function actionAddRole()
    {
        $models = new RoleForm();
        if($models->load(\Yii::$app->request->post()) && $models->validate()){
          if($models->addRole()){
                \Yii::$app->session->setFlash('success','角色添加成功');
                return $this->redirect(['role-index']);
            }
        }
       return $this->render('add-role',['models'=>$models]);
    }
    //修改角色
    public function actionEditRole($name){
        $role = \Yii::$app->authManager->getRole($name);
        if($role==null){
            throw new NotFoundHttpException('角色不存在');
        }

        $models = new RoleForm();
        $models->loadData($role);
        if($models->load(\Yii::$app->request->post()) && $models->validate()){
            if($models->updateRole($name)){
                \Yii::$app->session->setFlash('success','角色修改成功');
                return $this->redirect(['role-index']);
            }
        }

        return $this->render('add-role',['models'=>$models]);
    }
    //删除角色
    public function actionDataRole($name){
        $rbac = \Yii::$app->authManager->getRole($name);
        if($rbac == null){
            throw new NotFoundHttpException('角色不存在');
        }
        \Yii::$app->authManager->remove($rbac);
        \Yii::$app->session->setFlash('success','角色删除成功');
        return $this->redirect(['role-index']);
    }
    //角色列表
    public function actionRoleIndex()
    {
        $models = \Yii::$app->authManager->getRoles();

        return $this->render('role-index',['models'=>$models]);
    }
// 角色和权限关联

// 用户和角色关联
public function actionUsername(){
        $authManager = \Yii::$app->authManager;
        $authManager->getRoles();
        $role = $authManager->getRole('管理员');
        $authManager->assign($role,1);
        $authManager->revokeAll(1);
    }
    //过滤器
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
                // 'only'=>['add'],
            ]
        ];
    }
}
