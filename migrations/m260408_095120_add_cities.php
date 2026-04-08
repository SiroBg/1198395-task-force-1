<?php

use yii\db\Migration;

class m260408_095120_add_cities extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\Exception
     */
    public function safeUp(): void
    {
        $citiesSql = __DIR__ . '/../db/cities.sql';

        if (file_exists($citiesSql)) {
            $sql = file_get_contents($citiesSql);
            $this->execute($sql);
        } else {
            throw new \yii\base\Exception(
                "Отсутствует sql файл: $citiesSql. Выполните команду php sql-fill-cli.php,
             чтобы конвертировать файлы из data.csv в sql"
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260408_095120_add_cities cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260408_095120_add_cities cannot be reverted.\n";

        return false;
    }
    */
}
