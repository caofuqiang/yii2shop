<?php

namespace backend\controllers;

use backend\components\RbacFilter;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use backend\models\GoodsSearchForm;
use xj\uploadify\UploadAction;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\UploadedFile;
use backend\components\SphinxClient;

class GoodsController extends \yii\web\Controller
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
        // $key=isset($_GET['key'])? $_GET['key']: '';
        // $query = Goods::find()->andWhere(['like','name',$key]);

        $model = new GoodsSearchForm();
        $query = Goods::find();
        if($keyword = \Yii::$app->request->get('keyword')){
            $cl = new SphinxClient();
            $cl->SetServer ( '127.0.0.1', 9312);
            $cl->SetConnectTimeout ( 10 );
            $cl->SetArrayResult ( true );
            $cl->SetMatchMode ( SPH_MATCH_ALL);
            $cl->SetLimits(0, 1000);
            $res = $cl->Query($keyword, 'goods');//shopstore_search
            if(!isset($res['matches'])){
//                throw new NotFoundHttpException('没有找到xxx商品');
                $query->where(['id'=>0]);
            }else{

                //获取商品id
                //var_dump($res);exit;
                $ids = ArrayHelper::map($res['matches'],'id','id');
                $query->where(['in','id',$ids]);
            }
        }
        //$query=Goods::find()->where(['!=','status',-1]);
        $query = Goods::find()->where(['!=','status',-1]);
        $model->search($query);
        $pager = new Pagination([
            'totalCount'=>$query->count(),
            'defaultPageSize'=>5
        ]);
        $goods = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['goods'=>$goods,'pager'=>$pager,'model'=>$model]);
    }
    public function GoodsCategory()
    {
        $model=GoodsCategory::find()->all();
        return $this->render('index',['model'=>$model]);
    }
    //Brand找name名字的方法
    public function Brand()
    {
        $model=Brand::find()->all();
        return $this->render('index',['model'=>$model]);
    }
    //添加品牌
    public function actionAdd()
    {
        $model = new Goods();
        $request = new Request();
        $model2=new GoodsIntro();
        $cate=GoodsIntro::find()->asArray()->all();
        $count=ArrayHelper::map($cate,'id','content');
        $request=\Yii::$app->request;
        if($request->isPost) {
            $model->load($request->post());
            $model2->load($request->post());
            $model->create_time = time();
            //var_dump($model->goods_category_id);exit;
            /*自动生成商品货号*/
            if ($model->validate()) {
                //查询当天添加了几个商品,生成Sn
                $count = 0;//初始值为0计数，默认当天没有添加商品
                if($day = GoodsDayCount::findOne(['day'=> date('Y-m-d')])){//查询到当天如果有商品数据
                    $count += $day->count;//获取到原来的count值
                    $model->sn = date('Ymd').substr($count +10000,-4);//获取到sn
                    $day->count = $count+1;//新的count值
                    $day->save();//更新数据表
                }else{
                    //当天还没有添加商品,开始第一次添加表
                    $goods_day_count = new GoodsDayCount();
                    $goods_day_count->day = date('Y-m-d');
                    $goods_day_count->count = $count+1;
                    $goods_day_count->save();//保存每天添加商品的数量信息
                    $model->sn = date('Ymd').substr($count +10000,-4);//获取到sn
                }
                if($model2->validate()) {
                    $model2->goods_id = $model->id;
                    $model2->save();
                }
                $model->save();
            }
            \Yii::$app->session->setFlash('success', '添加成功');
            return $this->redirect(['goods/index','id'=>$model->id]);
        }
        // return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $categories = ArrayHelper::merge([['id'=>0,'name'=>'顶级分类','parent_id'=>0]],GoodsCategory::find()->asArray()->all());
        return $this->render('add',['model'=>$model,'categories'=>$categories,'model2'=>$model2]);


    }
    //修改品牌
    public function actionEdit($id)
    {
        $model = Goods::findOne(['id' => $id]);
        $request = new Request();
        $model2=new GoodsIntro();
        $cate=GoodsIntro::find()->asArray()->all();
        $count=ArrayHelper::map($cate,'id','content');
        $request=\Yii::$app->request;
        if($request->isPost) {
            $model->load($request->post());
            $model2->load($request->post());
            $model->create_time = time();
            //var_dump($model->goods_category_id);exit;
            /*自动生成商品货号*/
            if ($model->validate()) {
                //查询当天添加了几个商品,生成Sn
                $count = 0;//初始值为0计数，默认当天没有添加商品
                if($day = GoodsDayCount::findOne(['day'=> date('Y-m-d')])){//查询到当天如果有商品数据
                    $count += $day->count;//获取到原来的count值
                    $model->sn = date('Ymd').substr($count +10000,-4);//获取到sn
                    $day->count = $count+1;//新的count值
                    $day->save();//更新数据表
                }else{
                    //当天还没有添加商品,开始第一次添加表
                    $goods_day_count = new GoodsDayCount();
                    $goods_day_count->day = date('Y-m-d');
                    $goods_day_count->count = $count+1;
                    $goods_day_count->save();//保存每天添加商品的数量信息
                    $model->sn = date('Ymd').substr($count +10000,-4);//获取到sn
                }
                if($model2->validate()) {
                    $model2->goods_id = $model->id;
                    $model2->save();
                }
                $model->save();
            }
            \Yii::$app->session->setFlash('success', '修改成功');
            return $this->redirect(['goods/index','id'=>$model->id]);
        }
        // return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $categories = ArrayHelper::merge([['id' => 0, 'name' => '顶级分类', 'parent_id' => 0]], GoodsCategory::find()->asArray()->all());
        return $this->render('add', ['model' => $model, 'categories' => $categories,'model2'=>$model2]);
    }
    //删除品牌
    public function actionDel($id){
        $model = Goods::findOne(['id'=>$id]);
        $model->status= -1;
        $model->save();
        if($model==null){
            throw new NotFoundHttpException('品牌不存在');
        }
        $model->updateAttributes(['status'=>-1]);
        \Yii::$app->session->setFlash('success','品牌删除成功');
        return $this->redirect(['goods/index']);
    }
    /*
     * 商品相册
     */
    public function actionGallery($id)
    {
        $goods = Goods::findOne(['id'=>$id]);
        if($goods == null){
            throw new NotFoundHttpException('商品不存在');
        }


        return $this->render('gallery',['goods'=>$goods]);

    }

    /*
     * AJAX删除图片
     */
    public function actionDelGallery(){
        $id = \Yii::$app->request->post('id');
        $model = GoodsGallery::findOne(['id'=>$id]);
        if($model && $model->delete()){
            return 'success';
        }else{
            return 'fail';
        }

    }


    public function actions() {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    "imageUrlPrefix"  => "",//图片访问路径前缀
                    "imagePathFormat" => "/upload/{yyyy}{mm}{dd}/{time}{rand:6}" ,//上传保存路径
                    "imageRoot" => \Yii::getAlias("@webroot"),
                ],
            ],

            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload/logo',
                'baseUrl' => '@web/upload/logo',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
                /*'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filename = sha1_file($action->uploadfile->tempName);
                    return "{$filename}.{$fileext}";
                },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "/{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png','gif'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    //图片上传成功的同时，将图片和商品关联起来
                    $model = new GoodsGallery();
                    $model->goods_id = \Yii::$app->request->post('goods_id');
                    $model->path = $action->getWebUrl();
                    $model->save();
                    $action->output['fileUrl'] = $model->path;
                    //$action->output['goods_id'] = $model->goods_id;

//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    //$action->output['Path'] = $action->getSavePath();
                    /*
                     * 将图片上传到七牛云
                     */
                    /* $qiniu = \Yii::$app->qiniu;//实例化七牛云组件
                     $qiniu->uploadFile($action->getSavePath(),$action->getFilename());//将本地图片上传到七牛云
                     $url = $qiniu->getLink($action->getFilename());//获取图片在七牛云上的url地址
                     $action->output['fileUrl'] = $url;//将七牛云图片地址返回给前端js
                    */
                },
            ],
        ];
    }

   /* public function actionTest()
    {
        //echo substr('000'.'99',-4,4);
        //echo sprintf("%04d",4448);
        //echo str_pad('1',4,0,STR_PAD_LEFT);
        //return $this->render('test');


        //加盐加密
        $salt = '@woai?php*.com';
        $salt2 = time();
        $str = '123456';
        $password = md5($salt.$str.$salt2);
        $salt = '@woai?php*.com'.rand(100,999);
        $password2 = md5($str.$salt);
        //echo $password;
        echo '<br>';
        echo $password2;


        $str2= '123456';
        $salt = '@woai?php*.com'.rand(100,999);
        $password2 = md5($str.$salt);
        var_dump($password == $password2);
    }*/
    public function actionTest(){
        $cl = new SphinxClient();
        $cl->SetServer ( '127.0.0.1', 9312);
//$cl->SetServer ( '10.6.0.6', 9312);
//$cl->SetServer ( '10.6.0.22', 9312);
//$cl->SetServer ( '10.8.8.2', 9312);
        $cl->SetConnectTimeout ( 10 );
        $cl->SetArrayResult ( true );
// $cl->SetMatchMode ( SPH_MATCH_ANY);
        $cl->SetMatchMode ( SPH_MATCH_ALL);
        $cl->SetLimits(0, 1000);
        $info = '云集';//需要搜索的词
        $res = $cl->Query($info, 'goods');//shopstore_search
//print_r($cl);
        var_dump($res);
    }
}
