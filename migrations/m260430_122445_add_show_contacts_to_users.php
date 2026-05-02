<?php

use yii\db\Migration;

class m260430_122445_add_show_contacts_to_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('users', 'show_contacts', $this->boolean()->defaultValue(true)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260430_122445_add_show_contacts_to_users cannot be reverted.\n";

        return false;
    }
}
