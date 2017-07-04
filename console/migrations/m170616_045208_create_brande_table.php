<?php

use yii\db\Migration;

/**
 * Handles the creation of table `brande`.
 */
class m170616_045208_create_brande_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('brande', [
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
        $this->dropTable('brande');
    }
}
