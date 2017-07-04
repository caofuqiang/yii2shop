<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article_category`.
 */
class m170608_101901_create_article_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article_category', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->notNull()->comment('名称'),
//name varchar﴾50﴿ 名称
            'intro'=>$this->text()->comment('简介'),
//intro text 简介
            'sort'=>$this->integer()->comment('排除'),
//logo varchar﴾255﴿ LOGO图片
            'status'=>$this->smallInteger(2)->comment('状态'),
//sort int﴾11﴿ 排序
            'is_heip'=>$this->integer()->comment('类型'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article_category');
    }
}
