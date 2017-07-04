<?php

namespace backend\controllers;

use backend\components\RbacFilter;
use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class Article_categoryController extends \yii\web\Controller
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
    //文章列表
    public function actionIndex()
    {
        $query = ArticleCategory::find()->where(['!=','status',-1]);

        $pager = new Pagination([
            'totalCount'=>$query->count(),
            'defaultPageSize'=>10
        ]);
        $models = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }
    //添加文章
    public function actionAdd()
    {
        $model = new ArticleCategory();
        if($model->load(\Yii::$app->request->post())){
            $model->imgFile = UploadedFile::getInstance($model,'imgFile');
            if($model->validate()){
                if($model->imgFile){
                    $fileName = '/images/brand/'.uniqid().'.'.$model->imgFile->extension;
                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$fileName,false);
                    $model->logo = $fileName;
                }
                $model->save();
                \Yii::$app->session->setFlash('success','品牌添加成功');
                return $this->redirect(['article_category/index']);

            }
        }

        return $this->render('add',['model'=>$model]);

    }
    //修改文章
    public function actionEdit($id)
    {
        $model = ArticleCategory::findOne(['id'=>$id]);
        if($model==null){
            throw new NotFoundHttpException('品牌不存在');
        }
        if($model->load(\Yii::$app->request->post())){
            $model->imgFile = UploadedFile::getInstance($model,'imgFile');
            if($model->validate()){
                if($model->imgFile){
                    $fileName = '/images/article_category/'.uniqid().'.'.$model->imgFile->extension;
                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$fileName,false);
                    $model->logo = $fileName;
                }
                $model->save();
                \Yii::$app->session->setFlash('success','品牌添加成功');
                return $this->redirect(['article_category/index']);

            }
        }

        return $this->render('add',['model'=>$model]);
    }
    //删除文章
    public function actionDel($id){
        $model = ArticleCategory::findOne(['id'=>$id]);
        $model->status= -1;
        $model->save();
        if($model==null){
            throw new NotFoundHttpException('品牌不存在');
        }
        // $model->updateAttributes(['status'=>-1]);
        \Yii::$app->session->setFlash('success','品牌删除成功');
        return $this->redirect(['article_category/index']);
    }

}
