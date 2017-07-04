<?php

namespace backend\controllers;

use backend\components\RbacFilter;
use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class ArticleController extends \yii\web\Controller
{
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
    public function actionIndex()
    {
        $query=Article::find()->where(['!=','status',-1]);
        $total=$query->count();
        $page=new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>3,
        ]);
        $model=$query->offset($page->offset)->limit($page->limit)->all();
        return $this->render('index',['model'=>$model,'page'=>$page]);
    }
    public function actionArticleCategory()
    {
        $model=ArticleCategory::find()->all();
        return $this->render('index',['model'=>$model]);
    }
    //添加数据
    public function actionAdd(){
        $model=new Article();
        $model2=new ArticleDetail();
        //查询文章分类列表数据
        $cate=ArticleCategory::find()->asArray()->where(['status'=>1])->all();
//        $cate=ArticleCategory::find()->asArray()->all();
        //遍历文章分类
//        $count=[];
//        foreach($cate as $v){
//            if($v['status']>=0){
//                $count[$v['id']]=$v['name'];
//            }
//        }
        $count=ArrayHelper::map($cate,'id','name');
        $request=\Yii::$app->request;
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            $model2->load($request->post());
            //验证数据
            if($model->validate()){
                $model->create_time=time();
                $model->save();
                //验证数据
                if($model2->validate()){
                    $model2->article_id=$model->id;
                    $model2->save();
                    \Yii::$app->session->setFlash('success','文章添加成功');
                    return $this->redirect(['article/index']);
                }
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model,'cate'=>$cate,'model2'=>$model2]);
    }
    //修改数据
    public function actionEdit($id){
        $model=Article::findOne(['id'=>$id]);
        $model2=ArticleDetail::findOne(['article_id'=>$id]);
        //查询文章分类列表数据           第一种
       /** $cate=ArticleCategory::find()->all();
        //遍历文章分类
        $count=[];
        foreach($cate as $v){
            if($v['status']>=0){
                $count[$v['id']]=$v['name'];
            }
        }*/
        //第二种
        $cate=ArticleCategory::find()->asArray()->where(['status'=>1])->all();
        $count=ArrayHelper::map($cate,'id','name');
        $request=\Yii::$app->request;
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            $model2->load($request->post());
            //验证数据
            if($model->validate()){
//                $model->create_time=time();
                $model->save();
                //验证数据
                if($model2->validate()){
                    $model2->article_id=$model->id;
                    $model2->save();
                    \Yii::$app->session->setFlash('success','文章修改成功');
                    return $this->redirect(['article/index']);
                }
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model,'cate'=>$cate,'model2'=>$model2]);
    }
    //删除数据
    public function actionDel($id){
        $model=Article::findOne(['id'=>$id]);
//        var_dump($model);exit;
        $model->status=-1;
        $model->save();
        \Yii::$app->session->setFlash('success','删除文章成功');
        return $this->redirect(['article/index']);
    }
    //查看文章详情
    public function actionList($id){
        $model2=ArticleDetail::findOne(['article_id'=>$id]);
        $model3=Article::findOne(['id'=>$id]);
        return $this->render('list',['model3'=>$model3,'model2'=>$model2]);
    }
//    //百度ueditor
//    public function actions()
//    {
//        return [
//            'upload' => [
//                'class' => 'kucha\ueditor\UEditorAction',
//            ]
//        ];
//    }
}
