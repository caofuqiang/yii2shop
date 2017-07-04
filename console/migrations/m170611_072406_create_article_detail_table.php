<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article_detail`.
 */
class m170611_072406_create_article_detail_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article_detail', [
            'day' => $this->primaryKey()->comment('文章id'),
            'count' => $this->integer()->notNull()->comment('内容'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article_detail');
    }
}
