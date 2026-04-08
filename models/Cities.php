<?php

namespace app\models;


/**
 * This is the model class for table "cities".
 *
 * @property int         $id
 * @property string|null $created_at
 * @property string      $name
 * @property float       $lat
 * @property float       $long
 *
 * @property Tasks[]     $tasks
 * @property Users[]     $users
 */
class Cities extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'cities';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['created_at'], 'safe'],
            [['name', 'lat', 'long'], 'required'],
            [['lat', 'long'], 'number'],
            [['name'], 'string', 'max' => 256],
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
            'lat'        => 'Lat',
            'long'       => 'Long',
        ];
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Tasks::class, ['city_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Users::class, ['city_id' => 'id']);
    }

}
