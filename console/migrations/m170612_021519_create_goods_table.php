<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods`.
 */
class m170612_021519_create_goods_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'sn' => $this->string()->notNull(),
            'logo' => $this->string(255)->notNull(),
            'goods_category_id' => $this->integer()->notNull(),
            'brand_id' => $this->string()->notNull(),
            'market_price'=>$this->decimal(10,2)->notNull(),
            'shop_price'=>$this->decimal(10,2)->notNull(),
            'stock'=>$this->integer()->notNull(),
            'is_on_sale'=>$this->integer()->notNull(),
            'status'=>$this->integer()->notNull(),
            'sort'=>$this->integer()->notNull(),
            'create_time'=>$this->integer()->notNull()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods');
    }
}
