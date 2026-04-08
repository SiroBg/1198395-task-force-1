<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the model class for table "tasks".
 *
 * @property int         $id
 * @property string|null $created_at
 * @property int         $author_id
 * @property int|null    $executor_id
 * @property string      $name
 * @property string      $description
 * @property int         $category_id
 * @property string      $location
 * @property float|null  $lat
 * @property float|null  $long
 * @property int|null    $city_id
 * @property int|null    $budget
 * @property string|null $expire_date
 * @property string|null $status
 *
 * @property Users       $author
 * @property Categories  $category
 * @property Cities      $city
 * @property Users       $executor
 * @property Responds[]  $responds
 * @property Reviews[]   $reviews
 * @property TaskFiles[] $taskFiles
 */
class Tasks extends \yii\db\ActiveRecord
{
    /**
     * ENUM field values
     */
    public const string STATUS_STATUS_NEW = 'status_new';
    public const string STATUS_STATUS_CANCELED = 'status_canceled';
    public const string STATUS_STATUS_ACTIVE = 'status_active';
    public const string STATUS_STATUS_FINISHED = 'status_finished';
    public const string STATUS_STATUS_FAILED = 'status_failed';

    public array $task_files = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [
                [
                    'executor_id',
                    'lat',
                    'long',
                    'city_id',
                    'budget',
                    'expire_date',
                    'status',
                ],
                'default',
                'value' => null,
            ],
            [['created_at', 'expire_date'], 'safe'],
            [
                'expire_date',
                'date',
                'format'   => 'php:Y-m-d',
                'min'      => date('Y-m-d'),
                'tooSmall' => 'Выберите дату позже ' . date('d.m.Y'),
            ],
            [
                ['author_id', 'name', 'description', 'category_id', 'location'],
                'required',
            ],
            [
                [
                    'author_id',
                    'executor_id',
                    'category_id',
                    'city_id',
                    'budget',
                ],
                'integer',
                'min' => 0,
            ],
            [['description', 'status'], 'string'],
            [
                'description',
                'validateStringLengthNoSpaces',
                'params' => ['length' => 30],
            ],
            [['lat', 'long'], 'number'],
            [['name', 'location'], 'string', 'max' => 256],
            [
                'name',
                'validateStringLengthNoSpaces',
                'params' => ['length' => 10],
            ],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [
                ['author_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Users::class,
                'targetAttribute' => ['author_id' => 'id'],
            ],
            [
                ['executor_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Users::class,
                'targetAttribute' => ['executor_id' => 'id'],
            ],
            [
                ['category_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Categories::class,
                'targetAttribute' => ['category_id' => 'id'],
            ],
            [
                ['city_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Cities::class,
                'targetAttribute' => ['city_id' => 'id'],
            ],
            ['task_files', 'file', 'maxFiles' => 0, 'skipOnEmpty' => true],
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
            'author_id'   => 'Author ID',
            'executor_id' => 'Executor ID',
            'name'        => 'Опишите суть работы',
            'description' => 'Подробности задания',
            'category_id' => 'Категория',
            'location'    => 'Локация',
            'lat'         => 'Lat',
            'long'        => 'Long',
            'city_id'     => 'City ID',
            'budget'      => 'Бюджет',
            'expire_date' => 'Срок исполнения',
            'status'      => 'Status',
            'task_files'  => 'Файлы',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return ActiveQuery
     */
    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(Users::class, ['id' => 'author_id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return ActiveQuery
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Categories::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return ActiveQuery
     */
    public function getCity(): ActiveQuery
    {
        return $this->hasOne(Cities::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return ActiveQuery
     */
    public function getExecutor(): ActiveQuery
    {
        return $this->hasOne(Users::class, ['id' => 'executor_id']);
    }

    /**
     * Gets query for [[Responds]].
     *
     * @return ActiveQuery
     */
    public function getResponds(): ActiveQuery
    {
        return $this->hasMany(Responds::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return ActiveQuery
     */
    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Reviews::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[TaskFiles]].
     *
     * @return ActiveQuery
     */
    public function getTaskFiles(): ActiveQuery
    {
        return $this->hasMany(TaskFiles::class, ['task_id' => 'id']);
    }

    /**
     * column status ENUM value labels
     *
     * @return string[]
     */
    public static function optsStatus(): array
    {
        return [
            self::STATUS_STATUS_NEW      => 'Новое',
            self::STATUS_STATUS_CANCELED => 'Отменено',
            self::STATUS_STATUS_ACTIVE   => 'Выполняется',
            self::STATUS_STATUS_FINISHED => 'Завершено',
            self::STATUS_STATUS_FAILED   => 'Провалено',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus(): string
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusStatusnew(): bool
    {
        return $this->status === self::STATUS_STATUS_NEW;
    }

    public function setStatusToStatusnew(): void
    {
        $this->status = self::STATUS_STATUS_NEW;
    }

    /**
     * @return bool
     */
    public function isStatusStatuscanceled(): bool
    {
        return $this->status === self::STATUS_STATUS_CANCELED;
    }

    public function setStatusToStatuscanceled(): void
    {
        $this->status = self::STATUS_STATUS_CANCELED;
    }

    /**
     * @return bool
     */
    public function isStatusStatusactive(): bool
    {
        return $this->status === self::STATUS_STATUS_ACTIVE;
    }

    public function setStatusToStatusactive(): void
    {
        $this->status = self::STATUS_STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isStatusStatusfinished(): bool
    {
        return $this->status === self::STATUS_STATUS_FINISHED;
    }

    public function setStatusToStatusfinished(): void
    {
        $this->status = self::STATUS_STATUS_FINISHED;
    }

    /**
     * @return bool
     */
    public function isStatusStatusfailed(): bool
    {
        return $this->status === self::STATUS_STATUS_FAILED;
    }

    public function setStatusToStatusfailed(): void
    {
        $this->status = self::STATUS_STATUS_FAILED;
    }

    public function validateStringLengthNoSpaces($attribute, $params): void
    {
        if (mb_strlen(trim($this->$attribute)) < $params['length']) {
            $this->addError(
                $attribute,
                'Длина поля должна быть не меньше '
                . $params['length']
                . ' символов.',
            );
        }
    }
}
