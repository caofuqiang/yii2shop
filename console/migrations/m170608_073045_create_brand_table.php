<?php

use yii\db\Migration;

/**
 * Handles the creation of table `brand`.
 */
class m170608_073045_create_brand_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('brand', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->comment('姓名'),
            'intro'=>$this->text()->comment('简介 '),
            'logo'=>$this->string(255)->comment('logo图片'),
            'sort'=>$this->integer(11)->comment('排序'),
            'status'=>$this->integer(2)->comment('状态')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('brand');
    }
}
