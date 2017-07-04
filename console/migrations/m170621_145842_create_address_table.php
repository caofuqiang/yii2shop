<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m170621_145842_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50),
            'province' => $this->integer(10),
            'city' => $this->string(10),
            'area' => $this->integer(10),
            'detail' => $this->string(50),
            'tel' => $this->string(20),
            'status' => $this->string(20),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
