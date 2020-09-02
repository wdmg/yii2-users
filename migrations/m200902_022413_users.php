<?php

use yii\db\Migration;

/**
 * Class m200902_022413_users
 */
class m200902_022413_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('{{%users}}', 'lastseen_at', $this->datetime()
            ->defaultExpression('CURRENT_TIMESTAMP')
            ->after('updated_at'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%users}}', 'lastseen_at');
    }
}
