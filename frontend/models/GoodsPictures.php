<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "goods_pictures".
 *
 * @property integer $id
 * @property string $img
 * @property integer $goods_id
 * @property integer $status
 */
class GoodsPictures extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_pictures';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'status'], 'integer'],
            [['img'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'img' => '商品图片',
            'goods_id' => '所属商品',
            'status' => 'Status',
        ];
    }
}
