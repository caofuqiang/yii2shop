<?php

namespace backend\models;

use creocoder\nestedsets\NestedSetsQueryBehavior;
use Yii;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "goods".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property string $logo
 * @property integer $goods_category_id
 * @property string $brand_id
 * @property string $market_price
 * @property string $shop_price
 * @property integer $stock
 * @property integer $is_on_sale
 * @property integer $status
 * @property integer $sort
 * @property integer $create_time
 */
class Goods extends \yii\db\ActiveRecord
{
    public $logo_file;
    static public $sexOption=[-1=>'删除',0=>'隐藏',1=>'正常'];
    static public $sexOptions=[0=>'下架',1=>'在售'];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods';
    }
    public static function getCategoryOptions()
    {
        return ArrayHelper::map(Brand::find()->where(['status'=>1])->asArray()->all(),'id','name');
    }
    public function getBrand()
    {
        //hasOne的第二个参数【k=>v】 k代表分类的主键（id） v代表商品分类在当前对象的关联id
        return $this->hasOne(Brand::className(),['id'=>'brand_id']);
    }
    public function getGoodsCategory()
    {
        //hasOne的第二个参数【k=>v】 k代表分类的主键（id） v代表商品分类在当前对象的关联id
        return $this->hasOne(GoodsCategory::className(),['id'=>'goods_category_id']);
    }
    /*
        * 商品和相册关系 1对多
        */
    public function getGalleries()
    {
        return $this->hasMany(GoodsGallery::className(),['goods_id'=>'id']);
    }

    public function getCate(){
        return $this->hasOne(Goods::className(),['id' => 'name']);
    }
    public function getIntro()
    {
        return $this->hasOne(GoodsIntro::className(),['goods_id'=>'id']);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'brand_id', 'market_price', 'shop_price', 'stock', 'is_on_sale', 'status', 'sort', 'create_time'], 'required'],
            [['goods_category_id', 'stock', 'is_on_sale', 'status', 'sort', 'create_time'], 'integer'],
            [['market_price', 'shop_price'], 'number'],
            [['name', 'sn', 'logo', 'brand_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商品名称',
            'sn' => '货号',
            'logo' => 'Logo图片',
            'goods_category_id' => '商品分类ID',
            'brand_id' => '品牌分类',
            'market_price' => '市场价格',
            'shop_price' => '商品价格',
            'stock' => '库存',
            'is_on_sale' => '是否在售（1在售 0下架）',
            'status' => '状态',
            'sort' => '排序',
            'create_time' => '添加时间',
            'inputtime' => 'Inputtime',
        ];
    }
}
