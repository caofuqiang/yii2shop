<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $name
 * @property string $intro
 * @property integer $article_category_id
 * @property integer $sort
 * @property integer $status
 * @property integer $create_time
 */
class Article extends \yii\db\ActiveRecord
{
    public static $status_options = [1=>'正常',0=>'隐藏',-1=>'删除'];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    public function getArticleCategory()
    {
        //hasOne的第二个参数【k=>v】 k代表分类的主键（id） v代表商品分类在当前对象的关联id
        return $this->hasOne(ArticleCategory::className(),['id'=>'article_category_id']);
    }
    public static function getCategoryOptions()
    {
        return ArrayHelper::map(ArticleCategory::find()->where(['status'=>1])->asArray()->all(),'id','name');
    }
    public function getDetail()
    {
        return $this->hasOne(ArticleDetail::className(),['article_id'=>'id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['intro'], 'string'],
            [['article_category_id', 'sort', 'status', 'create_time'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'intro' => '简介',
            'article_category_id' => '文章分类ID',
            'sort' => '排序',
            'status' => '状态',
            'create_time' => '创建时间',
        ];
    }
}
