<?php
namespace backend\controllers;

use backend\components\RbacFilter;
use backend\models\Menu;
use yii\helpers\ArrayHelper;

class MenuController extends \yii\web\Controller {

    public function actionAddMenu(){
        $model = new Menu();
        //接受数据，完成添加
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //1.根据url找到权限的描述名字

            $model->label = \Yii::$app->authManager->getPermission($model->url)->description;
            //2,判断是否为一级菜单
            if($model->parent_id == 0){
                //表示添加一级菜单
                if(!empty($model->menuName)){
                    $model->label = $model->menuName;//自定义一级菜单名称
                }
                $model->url =null;
            }
            $model->save(false);
            return $this->redirect(['menu/index']);
        }

        //获取所有权限，下拉显示
        $permissions = ArrayHelper::map( \Yii::$app->authManager->getPermissions(),'name','description');
        //获取menu中所有菜单项目，下拉显示
        $menus = Menu::find()->orderBy('id')->all();
        foreach ($menus as $menu){
            if($menu->parent_id != 0){
                $menu->label = str_repeat('—',5).$menu->label;
            }
        }
        $menus = ArrayHelper::map($menus,'id','label');
        $menus = ArrayHelper::merge(['0'=>'一级菜单'],$menus);//添加一级菜单选项

        return $this->render('add',['model'=>$model,'permissions'=>$permissions,'menus'=>$menus]);
    }


    //删除菜单delete_menu
    public function actionDeleteMenu($id){
        $menu = Menu::findOne(['id'=>$id])->delete();
        $this->redirect('index');
    }


    //菜单列表
    public function actionIndex(){
        $menus = Menu::find()->orderBy('id')->all();
        foreach ($menus as $menu){
            if($menu->parent_id != 0){
                $menu->label = str_repeat('—',5).$menu->label;
            }
        }
        return $this->render('index',['menus'=>$menus]);
    }


    //修改
    public function actionEditMenu($id){
        $model = Menu::findOne(['id'=>$id]);
        //接受数据，完成添加
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //1.根据url找到权限的描述名字
            $model->label = \Yii::$app->authManager->getPermission($model->url)->description;
            //2,判断是否为一级菜单
            if($model->parent_id == 0){
                //表示添加一级菜单
                if(!empty($model->menuName)){
                    $model->label = $model->menuName;//自定义一级菜单名称
                }
                $model->url =null;
            }
            $model->save(false);
            return $this->redirect(['menu/index']);
        }

        //获取所有权限，下拉显示
        $permissions = ArrayHelper::map( \Yii::$app->authManager->getPermissions(),'name','description');
        //获取menu中所有菜单项目，下拉显示
        $menus = ArrayHelper::map(Menu::find()->all(),'id','label');
        $menus = ArrayHelper::merge(['0'=>'一级菜单'],$menus);

        return $this->render('add',['model'=>$model,'permissions'=>$permissions,'menus'=>$menus]);
    }

   //过滤器
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
            ],
        ];
    }


}