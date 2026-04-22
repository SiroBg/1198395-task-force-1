<?php

use yii\db\Migration;

class m260408_094124_init extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\Exception
     */
    public function safeUp(): void
    {
        $schemaSql = __DIR__ . '/../db/schema.sql';

        if (file_exists($schemaSql)) {
            $sql = file_get_contents($schemaSql);
            $this->execute($sql);
        } else {
            throw new \yii\base\Exception(
                "Отсутствует sql файл: $schemaSql. Выполните команду php sql-fill-cli.php,
             чтобы конвертировать файлы из data.csv в sql",
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260408_094124_init cannot be reverted.\n";

        return false;
    }
}
