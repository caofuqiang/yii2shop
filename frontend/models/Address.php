<?php

namespace frontend\models;


use Yii;
/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property string $name
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $detail
 * @property string $tel
 * @property integer $status
 * @property integer $user_id
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','province','area','city','detail','tel'],'required'],
            [['status', 'user_id'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['province', 'city', 'area', 'detail'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => " * 收货人：",
            'province' => ' * 省份：',
            'city' => ' * 城市：',
            'area' => ' * 区县：',
            'detail' => ' * 详细地址：',
            'tel' => ' * 手机号码：',
            'status' => '是',
            'user_id' => '所属用户ID',
        ];
    }

    //保存收货地址
    public function saveaddress(){
        //判断是否为默认地址
        $addressall = Address::findAll(['user_id'=>\Yii::$app->user->id]);//找到所有
        foreach ($addressall as $address){
            $address->status = 0 ;//取消默认地址
            $address->save(false);
            $this->status = 1;
        }
        $this->user_id = Yii::$app->user->id;
        $this->save(false);
        return true;
    }


}
