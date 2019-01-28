<?php

use yii\db\Migration;

/**
 * Class m190128_181306_users
 */
class m190128_181306_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('{{%users}}', 'email_confirm_token', $this->string()->unique()->after('email'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%users}}', 'email_confirm_token');
    }
}
