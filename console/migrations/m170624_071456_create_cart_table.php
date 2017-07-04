<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cart`.
 */
class m170624_071456_create_cart_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('cart', [
            'id' => $this->primaryKey(),
            'goods_id' => $this->integer(20),
            'amount' => $this->integer(50),
            'member_id' => $this->integer(20),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('cart');
    }
}
