<?php

namespace backend\controllers;

use backend\components\RbacFilter;
use backend\models\Brand;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use xj\uploadify\UploadAction;
use crazyfd\qiniu\Qiniu;

class BrandController extends \yii\web\Controller
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
    //品牌列表
    public function actionIndex()
    {
        $query = Brand::find()->where(['!=','status',-1]);

        $pager = new Pagination([
            'totalCount'=>$query->count(),
            'defaultPageSize'=>10
        ]);
        $models = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }
    //添加品牌
    public function actionAdd()
    {
        $model = new Brand();
        if($model->load(\Yii::$app->request->post())){
           // $model->imgFile = UploadedFile::getInstance($model,'imgFile');
            if($model->validate()){
                //if($model->imgFile){
                   // $fileName = '/images/brand/'.uniqid().'.'.$model->imgFile->extension;
                   // $model->imgFile->saveAs(\Yii::getAlias('@webroot').$fileName,false);
                  //  $model->logo = $fileName;
               // }
                $model->save();
                \Yii::$app->session->setFlash('success','品牌添加成功');
                return $this->redirect(['brand/index']);

            }
        }

        return $this->render('add',['model'=>$model]);

    }
    //修改品牌
    public function actionEdit($id)
    {
        $model = Brand::findOne(['id'=>$id]);
        if($model->load(\Yii::$app->request->post())){
            // $model->imgFile = UploadedFile::getInstance($model,'imgFile');
            if($model->validate()){
                //if($model->imgFile){
                // $fileName = '/images/brand/'.uniqid().'.'.$model->imgFile->extension;
                // $model->imgFile->saveAs(\Yii::getAlias('@webroot').$fileName,false);
                //  $model->logo = $fileName;
                // }
                $model->save();
                \Yii::$app->session->setFlash('success','品牌添加成功');
                return $this->redirect(['brand/index']);

            }
        }

        return $this->render('add',['model'=>$model]);
    }
    //删除品牌
    public function actionDel($id){
        $model = Brand::findOne(['id'=>$id]);
        $model->status= -1;
        $model->save();
        if($model==null){
            throw new NotFoundHttpException('品牌不存在');
        }
        $model->updateAttributes(['status'=>-1]);
        \Yii::$app->session->setFlash('success','品牌删除成功');
        return $this->redirect(['brand/index']);
    }
    //图片上传
    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
                //'format' => function (UploadAction $action) {
                  //  $fileext = $action->uploadfile->getExtension();
               //     $filename = sha1_file($action->uploadfile->tempName);
                 //   return "{$filename}.{$fileext}";
               // },
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    $imgUrl = $action->getWebUrl();
                    //$action->output['fileUrl'] = $action->getWebUrl();
                    //调用七牛云组建，将图片上传到七牛云
                    $qiniu=\Yii::$app->qiniu;
                    //var_dump(qiniu);exit;
                    $qiniu->uploadFile(\Yii::getAlias('@webroot').$imgUrl,$imgUrl);
                    //获取该图片在七牛云的地址
                    $url =$qiniu->getLink($imgUrl);
                    $action->output['fileUrl'] = $url;
//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                },
            ],
        ];
    }
   public function actionTest(){
        $ak = 'FMhQxlxnpsZIwdsiURFtiswmxKRxw6AXruxdmzFe';
        $sk = 'TcZSp3v058c4HEgY6S8VngtrQZa_eOKi9deZ9esS';
        $domain = 'http://or9t6ktsf.bkt.clouddn.com/';
        $bucket = 'cfqphp0217';
        $qiniu = new Qiniu($ak, $sk,$domain, $bucket);
        $fileName = \Yii::getAlias('@webroot').'/upload/02.jpg';
        $key = '02.jpg';
        $re = $qiniu->uploadFile($fileName,$key);
       // var_dump($re);
        $url = $qiniu->getLink($key);
       var_dump($url);
    }
}
