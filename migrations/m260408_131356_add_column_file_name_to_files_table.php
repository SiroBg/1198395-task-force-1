<?php

use yii\db\Migration;

class m260408_131356_add_column_file_name_to_files_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->addColumn('files', 'name', $this->string()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260408_131356_add_column_file_name_to_files_table cannot be reverted.\n";

        return false;
    }
}
