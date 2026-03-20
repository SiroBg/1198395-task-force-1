<?php

namespace app\models;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string|null $created_at
 * @property int $author_id
 * @property int|null $executor_id
 * @property string $name
 * @property string $description
 * @property int $category_id
 * @property string $location
 * @property float|null $lat
 * @property float|null $long
 * @property int|null $city_id
 * @property int|null $budget
 * @property string|null $expire_date
 * @property string|null $status
 *
 * @property Users $author
 * @property Categories $category
 * @property Cities $city
 * @property Users $executor
 * @property Responds[] $responds
 * @property Reviews[] $reviews
 * @property TaskFiles[] $taskFiles
 */
class Tasks extends \yii\db\ActiveRecord
{
    /**
     * ENUM field values
     */
    public const STATUS_STATUS_NEW = 'status_new';
    public const STATUS_STATUS_CANCELED = 'status_canceled';
    public const STATUS_STATUS_ACTIVE = 'status_active';
    public const STATUS_STATUS_FINISHED = 'status_finished';
    public const STATUS_STATUS_FAILED = 'status_failed';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['executor_id', 'lat', 'long', 'city_id', 'budget', 'expire_date', 'status'], 'default', 'value' => null],
            [['created_at', 'expire_date'], 'safe'],
            [['author_id', 'name', 'description', 'category_id', 'location'], 'required'],
            [['author_id', 'executor_id', 'category_id', 'city_id', 'budget'], 'integer'],
            [['description', 'status'], 'string'],
            [['lat', 'long'], 'number'],
            [['name', 'location'], 'string', 'max' => 256],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['executor_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categories::class, 'targetAttribute' => ['category_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::class, 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'author_id' => 'Author ID',
            'executor_id' => 'Executor ID',
            'name' => 'Name',
            'description' => 'Description',
            'category_id' => 'Category ID',
            'location' => 'Location',
            'lat' => 'Lat',
            'long' => 'Long',
            'city_id' => 'City ID',
            'budget' => 'Budget',
            'expire_date' => 'Expire Date',
            'status' => 'Status',
        ];
    }

    public function getNewTasks()
    {
        return $this->find()->where(['status' => self::STATUS_STATUS_NEW])->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Users::class, ['id' => 'author_id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Categories::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(Cities::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutor()
    {
        return $this->hasOne(Users::class, ['id' => 'executor_id']);
    }

    /**
     * Gets query for [[Responds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponds()
    {
        return $this->hasMany(Responds::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Reviews::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[TaskFiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskFiles()
    {
        return $this->hasMany(TaskFiles::class, ['task_id' => 'id']);
    }

    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_STATUS_NEW => 'status_new',
            self::STATUS_STATUS_CANCELED => 'status_canceled',
            self::STATUS_STATUS_ACTIVE => 'status_active',
            self::STATUS_STATUS_FINISHED => 'status_finished',
            self::STATUS_STATUS_FAILED => 'status_failed',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusStatusnew()
    {
        return $this->status === self::STATUS_STATUS_NEW;
    }

    public function setStatusToStatusnew()
    {
        $this->status = self::STATUS_STATUS_NEW;
    }

    /**
     * @return bool
     */
    public function isStatusStatuscanceled()
    {
        return $this->status === self::STATUS_STATUS_CANCELED;
    }

    public function setStatusToStatuscanceled()
    {
        $this->status = self::STATUS_STATUS_CANCELED;
    }

    /**
     * @return bool
     */
    public function isStatusStatusactive()
    {
        return $this->status === self::STATUS_STATUS_ACTIVE;
    }

    public function setStatusToStatusactive()
    {
        $this->status = self::STATUS_STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isStatusStatusfinished()
    {
        return $this->status === self::STATUS_STATUS_FINISHED;
    }

    public function setStatusToStatusfinished()
    {
        $this->status = self::STATUS_STATUS_FINISHED;
    }

    /**
     * @return bool
     */
    public function isStatusStatusfailed()
    {
        return $this->status === self::STATUS_STATUS_FAILED;
    }

    public function setStatusToStatusfailed()
    {
        $this->status = self::STATUS_STATUS_FAILED;
    }
}
