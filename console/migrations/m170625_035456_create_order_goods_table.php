<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order_goods`.
 */
class m170625_035456_create_order_goods_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('order_goods', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer(20)->comment('订单id'),
            'goods_id' => $this->integer(20)->comment('商品id'),
            'goods_name' => $this->string(250)->comment('商品名称'),
            'logo' => $this->string(250)->comment('图片'),
            'price' => $this->decimal(20)->comment('价格'),
            'amount' => $this->integer(20)->comment('数量'),
            'total' => $this->decimal(20)->comment('小计'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('order_goods');
    }
}
