<?php

namespace app\models;


/**
 * Модель для таблицы городов "cities".
 *
 * @property int         $id
 * @property string|null $created_at
 * @property string      $name
 * @property float       $lat
 * @property float       $long
 *
 * @property Task[]      $tasks
 * @property User[]      $users
 */
class City extends \yii\db\ActiveRecord
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
     * Получает ActiveQuery для [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers(): \yii\db\ActiveQuery
    {
        return $this->hasMany(User::class, ['city_id' => 'id']);
    }

    /**
     * Получает ActiveQuery для [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Task::class, ['city_id' => 'id']);
    }

}
