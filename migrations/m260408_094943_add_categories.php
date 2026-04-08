<?php

use yii\db\Migration;

class m260408_094943_add_categories extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\Exception
     */
    public function safeUp(): void
    {
        $categoriesSql = __DIR__ . '/../db/categories.sql';

        if (file_exists($categoriesSql)) {
            $sql = file_get_contents($categoriesSql);
            $this->execute($sql);
        } else {
            throw new \yii\base\Exception(
                "Отсутствует sql файл: $categoriesSql. Выполните команду php sql-fill-cli.php,
             чтобы конвертировать файлы из data.csv в sql"
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260408_094943_add_categories cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260408_094943_add_categories cannot be reverted.\n";

        return false;
    }
    */
}
