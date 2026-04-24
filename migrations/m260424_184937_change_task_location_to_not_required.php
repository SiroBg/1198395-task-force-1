<?php

use yii\db\Migration;

class m260424_184937_change_task_location_to_not_required extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->alterColumn(
            'tasks',
            'location',
            $this->string(256)->null()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260424_184937_change_task_location_to_not_required cannot be reverted.\n";

        return false;
    }
}
