<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_intro`.
 */
class m170611_074439_create_goods_intro_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods_intro', [
            'goods_id' => $this->primaryKey()->comment('商品id'),
            'content' => $this->text()->notNull()->comment('商品，描述'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods_intro');
    }
}
