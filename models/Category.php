<?php

namespace app\models;

/**
 * This is the model class for table "categories".
 *
 * @property int            $id
 * @property string|null    $created_at
 * @property string         $name
 * @property string         $icon
 *
 * @property Task[]         $tasks
 * @property UserCategory[] $userCategories
 */
class Category extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['created_at'], 'safe'],
            [['name', 'icon'], 'required'],
            [['name', 'icon'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'         => 'ID',
            'created_at' => 'Created At',
            'name'       => 'Name',
            'icon'       => 'Icon',
        ];
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Task::class, ['category_id' => 'id']);
    }

    /**
     * Gets query for [[UserCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserCategories(): \yii\db\ActiveQuery
    {
        return $this->hasMany(UserCategory::class, ['category_id' => 'id']);
    }

}
