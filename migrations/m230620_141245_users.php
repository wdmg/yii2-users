<?php

use yii\db\Migration;

/**
 * Class m230620_141245_users
 */
class m230620_141245_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('{{%users}}', 'options', $this->json()
            ->null()
            ->after('status'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%users}}', 'options');
    }
}
