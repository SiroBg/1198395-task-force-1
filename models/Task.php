<?php

namespace app\models;

use app\actions\ActionAbstract;
use app\actions\ActionCancel;
use app\actions\ActionFinish;
use app\actions\ActionRefuse;
use app\actions\ActionRespond;
use app\actions\ActionStart;
use app\exceptions\TaskStatusException;
use Yii;
use yii\db\ActiveQuery;

/**
 * Модель для таблицы заданий "tasks".
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
 * @property User        $author
 * @property Category    $category
 * @property City        $city
 * @property User        $executor
 * @property Respond[]   $responds
 * @property Review[]    $reviews
 * @property TaskFile[]  $taskFiles
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * ENUM поля статусов задания.
     */
    public const string STATUS_NEW      = 'status_new';
    public const string STATUS_CANCELED = 'status_canceled';
    public const string STATUS_ACTIVE   = 'status_active';
    public const string STATUS_FINISHED = 'status_finished';
    public const string STATUS_FAILED   = 'status_failed';

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
            [
                [
                    'created_at',
                    'expire_date',
                    'location',
                ],
                'safe',
            ],
            [
                'expire_date',
                'date',
                'format'   => 'php:Y-m-d',
                'min'      => date('Y-m-d'),
                'tooSmall' => 'Выберите дату позже '.date('d.m.Y'),
            ],
            [
                ['author_id', 'name', 'description', 'category_id'],
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
            ['location', 'validateLocation', 'on' => 'create'],
            [['lat', 'long', 'city_id'], 'clearIfEmpty'],
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
                'targetClass'     => User::class,
                'targetAttribute' => ['author_id' => 'id'],
            ],
            [
                ['executor_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => User::class,
                'targetAttribute' => ['executor_id' => 'id'],
            ],
            [
                ['category_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Category::class,
                'targetAttribute' => ['category_id' => 'id'],
            ],
            [
                ['city_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => City::class,
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
     * Получает ActiveQuery для [[Author]].
     *
     * @return ActiveQuery
     */
    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    /**
     * Получает ActiveQuery для [[Category]].
     *
     * @return ActiveQuery
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Получает ActiveQuery для [[City]].
     *
     * @return ActiveQuery
     */
    public function getCity(): ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Получает ActiveQuery для [[Executor]].
     *
     * @return ActiveQuery
     */
    public function getExecutor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'executor_id']);
    }

    /**
     * Получает ActiveQuery для [[Responds]].
     *
     * @return ActiveQuery
     */
    public function getResponds(): ActiveQuery
    {
        return $this->hasMany(Respond::class, ['task_id' => 'id']);
    }

    /**
     * Получает ActiveQuery для [[Reviews]].
     *
     * @return ActiveQuery
     */
    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['task_id' => 'id']);
    }

    /**
     * Получает ActiveQuery для[[TaskFiles]].
     *
     * @return ActiveQuery
     */
    public function getTaskFiles(): ActiveQuery
    {
        return $this->hasMany(TaskFile::class, ['task_id' => 'id']);
    }

    /**
     * Описания для ENUM значений статусов задания.
     *
     * @return string[]
     */
    public static function optsStatus(): array
    {
        return [
            self::STATUS_NEW      => 'Новое',
            self::STATUS_CANCELED => 'Отменено',
            self::STATUS_ACTIVE   => 'Выполняется',
            self::STATUS_FINISHED => 'Завершено',
            self::STATUS_FAILED   => 'Провалено',
        ];
    }

    /**
     * Отображает описание статуса.
     * @return string
     */
    public function displayStatus(): string
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * Валидирует длину строки без учета пробелов.
     * @param $attribute
     * @param $params
     * @return void
     */
    public function validateStringLengthNoSpaces($attribute, $params): void
    {
        if (mb_strlen(trim($this->$attribute)) < $params['length']) {
            $this->addError(
                $attribute,
                'Длина поля должна быть не меньше '
                .$params['length']
                .' символов.',
            );
        }
    }

    /**
     * Валидирует локацию задания.
     * @param $attribute
     * @param $params
     * @return void
     */
    public function validateLocation($attribute, $params): void
    {
        $objectData = Yii::$app->yandexGeoCoder->getObjectData(
            $this->$attribute,
        );

        $searchedCity = array_find(
            City::find()->all(),
            function ($city) use ($objectData) {
                return str_contains($objectData['fullAddress'], $city->name);
            },
        );

        if ( ! $searchedCity) {
            $this->addError(
                $attribute,
                'Выбранного города нет в базе данных',
            );
        } else {
            $this->lat     = $objectData['coordinates'][1];
            $this->long    = $objectData['coordinates'][0];
            $this->city_id = $searchedCity->id;
        }
    }

    /**
     * Очищает значения координат при пустой локации.
     * @param $attribute
     * @param $params
     * @return void
     */
    public function clearIfEmpty($attribute, $params): void
    {
        if (empty($this->location)) {
            $this->$attribute = null;
        }
    }

    /**
     * Получает статус, в который перейдёт задание после примененного действия.
     *
     * @param  ActionAbstract  $action  Объект класса AbstractAction
     *
     * @return string Статус задания.
     */
    public function getNextStatus(
        ActionAbstract $action,
    ): string {
        return match ($action->getName()) {
            new ActionRespond()->getName() => self::STATUS_NEW,
            new ActionStart()->getName()   => self::STATUS_ACTIVE,
            new ActionCancel()->getName()  => self::STATUS_CANCELED,
            new ActionFinish()->getName()  => self::STATUS_FINISHED,
            new ActionRefuse()->getName()  => self::STATUS_FAILED,
        };
    }

    /**
     * Получает список доступных действий в зависимости от статуса задания.
     * @return array
     * @throws TaskStatusException
     */
    public function getActions(): array
    {
        $actionsToStatus = [
            self::STATUS_NEW      =>
                [
                    new ActionCancel(),
                    new ActionRespond(),
                    new ActionStart(),
                ],
            self::STATUS_ACTIVE   =>
                [
                    new ActionFinish(),
                    new ActionRefuse(),
                ],
            self::STATUS_CANCELED => [],
            self::STATUS_FAILED   => [],
            self::STATUS_FINISHED => [],
        ];

        if ( ! isset($actionsToStatus[$this->status])) {
            throw new TaskStatusException(
                'Статус задания не предусмотрен',
            );
        }

        return $actionsToStatus[$this->status];
    }
}
