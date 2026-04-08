<?php

namespace app\models;

/**
 * This is the model class for table "user_categories".
 *
 * @property int         $id
 * @property string|null $created_at
 * @property int         $user_id
 * @property int         $category_id
 *
 * @property Categories  $category
 * @property Users       $user
 */
class UserCategories extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user_categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['created_at'], 'safe'],
            [['user_id', 'category_id'], 'required'],
            [['user_id', 'category_id'], 'integer'],
            [
                ['category_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Categories::class,
                'targetAttribute' => ['category_id' => 'id']
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Users::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'          => 'ID',
            'created_at'  => 'Created At',
            'user_id'     => 'User ID',
            'category_id' => 'Category ID',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Categories::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

}
