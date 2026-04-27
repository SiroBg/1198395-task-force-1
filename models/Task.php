<?php

namespace app\models;

use app\actions\actionAbstract;
use app\actions\actionCancel;
use app\actions\actionFinish;
use app\actions\actionRefuse;
use app\actions\actionRespond;
use app\actions\actionStart;
use app\exceptions\TaskStatusException;
use Yii;
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
     * ENUM field values
     */
    public const string STATUS_NEW = 'status_new';
    public const string STATUS_CANCELED = 'status_canceled';
    public const string STATUS_ACTIVE = 'status_active';
    public const string STATUS_FINISHED = 'status_finished';
    public const string STATUS_FAILED = 'status_failed';

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
                'format' => 'php:Y-m-d',
                'min' => date('Y-m-d'),
                'tooSmall' => 'Выберите дату позже ' . date('d.m.Y'),
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
            ['location', 'validateLocation'],
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
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['author_id' => 'id'],
            ],
            [
                ['executor_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['executor_id' => 'id'],
            ],
            [
                ['category_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Category::class,
                'targetAttribute' => ['category_id' => 'id'],
            ],
            [
                ['city_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => City::class,
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
            'id' => 'ID',
            'created_at' => 'Created At',
            'author_id' => 'Author ID',
            'executor_id' => 'Executor ID',
            'name' => 'Опишите суть работы',
            'description' => 'Подробности задания',
            'category_id' => 'Категория',
            'location' => 'Локация',
            'lat' => 'Lat',
            'long' => 'Long',
            'city_id' => 'City ID',
            'budget' => 'Бюджет',
            'expire_date' => 'Срок исполнения',
            'status' => 'Status',
            'task_files' => 'Файлы',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return ActiveQuery
     */
    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return ActiveQuery
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return ActiveQuery
     */
    public function getCity(): ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return ActiveQuery
     */
    public function getExecutor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'executor_id']);
    }

    /**
     * Gets query for [[Responds]].
     *
     * @return ActiveQuery
     */
    public function getResponds(): ActiveQuery
    {
        return $this->hasMany(Respond::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return ActiveQuery
     */
    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[TaskFiles]].
     *
     * @return ActiveQuery
     */
    public function getTaskFiles(): ActiveQuery
    {
        return $this->hasMany(TaskFile::class, ['task_id' => 'id']);
    }

    /**
     * column status ENUM value labels
     *
     * @return string[]
     */
    public static function optsStatus(): array
    {
        return [
            self::STATUS_NEW => 'Новое',
            self::STATUS_CANCELED => 'Отменено',
            self::STATUS_ACTIVE => 'Выполняется',
            self::STATUS_FINISHED => 'Завершено',
            self::STATUS_FAILED => 'Провалено',
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
    public function isStatusNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function setStatusToNew(): void
    {
        $this->status = self::STATUS_NEW;
    }

    /**
     * @return bool
     */
    public function isStatusCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }

    public function setStatusToCanceled(): void
    {
        $this->status = self::STATUS_CANCELED;
    }

    /**
     * @return bool
     */
    public function isStatusActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function setStatusToActive(): void
    {
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isStatusFinished(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }

    public function setStatusToFinished(): void
    {
        $this->status = self::STATUS_FINISHED;
    }

    /**
     * @return bool
     */
    public function isStatusFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function setStatusToFailed(): void
    {
        $this->status = self::STATUS_FAILED;
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

    public function validateLocation($attribute, $params): void
    {
        $objectData = Yii::$app->yandexGeoCoder->getObjectData(
            $this->$attribute,
        );

        $userCity = User::find()
            ->where(['id' => Yii::$app->user->id])->one()->city;

        $isRightCity = array_any(
            $objectData['addressComponents'],
            function ($value) use ($userCity) {
                return in_array($userCity->name, $value);
            },
        );

        if (!$isRightCity) {
            $this->addError(
                $attribute,
                'Выберите адрес, находящийся в пределах вашего города',
            );
        } else {
            $this->lat = $objectData['coordinates'][1];
            $this->long = $objectData['coordinates'][0];
            $this->city_id = $userCity->id;
        }
    }

    public function clearIfEmpty($attribute, $params): void
    {
        if (empty($this->location)) {
            $this->$attribute = null;
        }
    }

    /**
     * Получает статус, в который перейдёт задание после примененного действия.
     *
     * @param actionAbstract $action Объект класса AbstractAction
     *
     * @return string Статус задания.
     */
    public function getNextStatus(
        actionAbstract $action,
    ): string {
        return match ($action->getName()) {
            actionRespond::getName() => self::STATUS_NEW,
            actionStart::getName() => self::STATUS_ACTIVE,
            actionCancel::getName() => self::STATUS_CANCELED,
            actionFinish::getName() => self::STATUS_FINISHED,
            actionRefuse::getName() => self::STATUS_FAILED,
        };
    }

    /**
     * Получает доступные действия над заданием для пользователя по статусу задания и Id.
     *
     * @param int  $userId     Id пользователя.
     * @param bool $isExecutor Является ли пользователь исполнителем.
     *
     * @return array Массив с объектами-потомками класса AbstractAction.
     * @throws TaskStatusException Исключение при непредусмотренном статусе задания.
     *
     */
    public function getActions(
        int $userId,
        bool $isExecutor,
    ): array {
        $actionsToStatus = [
            self::STATUS_NEW =>
                [
                    new actionCancel(),
                    new actionRespond(),
                    new actionStart(),
                ],
            self::STATUS_ACTIVE =>
                [
                    new actionFinish(),
                    new actionRefuse(),
                ],
            self::STATUS_CANCELED => [],
            self::STATUS_FAILED => [],
            self::STATUS_FINISHED => [],
        ];

        if (!isset($actionsToStatus[$this->status])) {
            throw new TaskStatusException(
                'Статус задания не предусмотрен',
            );
        }

        return array_filter(
            $actionsToStatus[$this->status],
            function ($action) use ($userId, $isExecutor) {
                return $action->checkRights(
                    $this->executor_id,
                    $this->author_id,
                    $userId,
                    $isExecutor,
                );
            },
        );
    }

    /**
     * Применяет действие к заданию, если оно возможно для текущего статуса.
     *
     * @param actionAbstract $action     Действие.
     * @param int            $userId     Id пользователя.
     * @param bool           $isExecutor Является ли пользователь исполнителем.
     * @param ?int           $executorId Id исполнителя (при действии "начать задание").
     *
     * @return bool `true` - действие применилось, `false` - действие невозможно.
     * @throws TaskStatusException
     */
    public function applyAction(
        actionAbstract $action,
        int $userId,
        bool $isExecutor,
        ?int $executorId = null,
    ): bool {
        $result = false;

        $currentActionsNames = array_map(
            function ($action) {
                return $action->getName();
            },
            $this->getActions($userId, $isExecutor),
        );

        if (in_array($action->getName(), $currentActionsNames)
        ) {
            if (
                $action->getName() === actionStart::getName()
                && !is_null($executorId)
            ) {
                $this->executor_id = $executorId;
            }

            $this->status = $this->getNextStatus($action);
            $result = true;
        }

        return $result;
    }
}
