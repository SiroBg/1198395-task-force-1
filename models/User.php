<?php

namespace app\models;

use DateInterval;
use DateTime;
use Yii;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

/**
 * Модель для таблицы пользователей "users".
 *
 * @property int            $id
 * @property string|null    $created_at
 * @property string         $email
 * @property string         $name
 * @property int            $city_id
 * @property string         $password
 * @property int            $is_executor
 * @property int|null       $profile_img_file_id
 * @property string|null    $birthday
 * @property string|null    $phone
 * @property string|null    $telegram
 * @property string|null    $about
 * @property bool           $show_contacts
 *
 * @property City           $city
 * @property File           $profileImgFile
 * @property Respond[]      $responds
 * @property Review[]       $reviews
 * @property Review[]       $reviewsAsExecutor
 * @property Task[]         $tasks
 * @property Task[]         $tasksAsExecutor
 * @property int            $finishedTasksAmount
 * @property int            $failedTasksAmount
 * @property UserCategory[] $userCategories
 * @property float          $rating
 * @property int|false      $ratingPlacement
 * @property bool           $isBusy
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    public string                   $password_retype = '';
    public array|string             $categories      = [];
    public null|UploadedFile|string $avatar          = null;

    /**
     * {@inheritDoc}
     * @param $id
     * @return User|IdentityInterface|null
     */
    public static function findIdentity($id): User|IdentityInterface|null
    {
        return self::findOne($id);
    }

    /**
     * {@inheritDoc}
     * @param $token
     * @param $type
     * @return void
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
    }

    /**
     * {@inheritDoc}
     * @return array|int|mixed|string|null
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritDoc}
     * @return void
     */
    public function getAuthKey()
    {
    }

    /**
     * {@inheritDoc}
     * @param $authKey
     * @return void
     */
    public function validateAuthKey($authKey)
    {
    }

    /**
     * Валидирует пароль пользователя.
     * @param $password
     * @return bool
     */
    public function validatePassword($password): bool
    {
        return Yii::$app->security->validatePassword(
            $password,
            $this->password,
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P18Y'));
        $maxDate = $date->format('Y-m-d');
        return [
            [
                [
                    'profile_img_file_id',
                    'birthday',
                    'phone',
                    'telegram',
                    'about',
                ],
                'default',
                'value' => null,
            ],
            [['created_at', 'birthday', 'show_contacts', 'categories'], 'safe'],
            [
                'birthday',
                'date',
                'format' => 'php:Y-m-d',
                'max'    => $maxDate,
                'tooBig' => 'Вам должно быть не меньше 18 лет. Выберите дату раньше '.$maxDate,
            ],
            [['show_contacts'], 'boolean'],
            [
                [
                    'email',
                    'name',
                    'city_id',
                    'is_executor',
                ],
                'required',
            ],
            [['password', 'password_retype'], 'required', 'on' => ['validatePassword', 'signup']],
            [['city_id', 'is_executor', 'profile_img_file_id'], 'integer'],
            [['about'], 'string'],
            [['email', 'name'], 'string', 'max' => 256],
            [['password'], 'string', 'min' => 8, 'max' => 128],
            [
                'password_retype',
                'compare',
                'compareAttribute' => 'password',
                'message'          => 'Пароли не совпадают',
                'on'               => ['signup', 'validatePassword'],
            ],
            [['phone'], 'string', 'max' => 11],
            ['phone', 'match', 'pattern' => '/^[0-9]+$/'],
            [['telegram'], 'string', 'max' => 64],
            [['email'], 'unique'],
            [
                ['profile_img_file_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => File::class,
                'targetAttribute' => ['profile_img_file_id' => 'id'],
            ],
            [
                ['city_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => City::class,
                'targetAttribute' => ['city_id' => 'id'],
            ],
            [
                'avatar',
                'file',
                'skipOnEmpty'              => true,
                'extensions'               => 'png, jpg, jpeg',
                'mimeTypes'                => 'image/jpeg, image/png',
                'checkExtensionByMimeType' => true,
                'maxSize'                  => 1024 * 1024 * 2,
                'wrongExtension'           => 'Выберите файл jpeg, jpg или png'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'                  => 'ID',
            'created_at'          => 'Created At',
            'email'               => 'Email',
            'name'                => 'Ваше имя',
            'city_id'             => 'Город',
            'password'            => 'Пароль',
            'password_retype'     => 'Повтор пароля',
            'is_executor'         => 'я собираюсь откликаться на заказы',
            'profile_img_file_id' => 'Profile Img File ID',
            'birthday'            => 'День рождения',
            'phone'               => 'Номер телефона',
            'telegram'            => 'Telegram',
            'about'               => 'Информация о себе',
            'show_contacts'       => 'Показывать контактную информацию',
        ];
    }

    /**
     * Получает ActiveQuery для [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity(): \yii\db\ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Получает ActiveQuery для [[ProfileImgFile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfileImgFile(): \yii\db\ActiveQuery
    {
        return $this->hasOne(File::class, ['id' => 'profile_img_file_id']);
    }

    /**
     * Получает ActiveQuery для [[Responds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponds(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Respond::class, ['executor_id' => 'id']);
    }

    /**
     * Получает ActiveQuery для [[Reviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Review::class, ['author_id' => 'id']);
    }

    /**
     * Получает ActiveQuery для [[ReviewsAsExecutor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviewsAsExecutor(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Review::class, ['executor_id' => 'id']);
    }

    /**
     * Получает ActiveQuery для [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Task::class, ['author_id' => 'id']);
    }

    /**
     * Получает ActiveQuery для [[Tasks0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasksAsExecutor(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Task::class, ['executor_id' => 'id']);
    }

    /**
     * Получает количество завершенных заданий.
     * @return int
     */
    public function getFinishedTasksAmount(): int
    {
        return count($this->getTasksAsExecutor()->andWhere(['status' => Task::STATUS_FINISHED])->all());
    }

    /**
     * Получает количество проваленных заданий.
     * @return int
     */
    public function getFailedTasksAmount(): int
    {
        return count($this->getTasksAsExecutor()->andWhere(['status' => Task::STATUS_FAILED])->all());
    }

    /**
     * Получает ActiveQuery для [[UserCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserCategories(): \yii\db\ActiveQuery
    {
        return $this->hasMany(UserCategory::class, ['user_id' => 'id']);
    }

    /**
     * Получает имя пользователя.
     * @return User|void|null
     */
    public function getName()
    {
        if ($id = Yii::$app->user->getId()) {
            return self::findOne($id);
        }
    }

    /**
     * Получает рейтинг пользователя.
     * @return float|int
     */
    public function getRating(): float|int
    {
        $result = 0;

        $ratingSum    = Review::find()->where(['executor_id' => $this->id])->sum(
            'rating',
        );
        $reviewsCount = count($this->reviewsAsExecutor);
        $failedTasks  = count(
            Task::find()->where(
                ['executor_id' => $this->id, 'status' => Task::STATUS_FAILED],
            )->all(),
        );

        if ($reviewsCount + $failedTasks !== 0) {
            $result = $ratingSum / ($reviewsCount + $failedTasks);
        }

        return $result;
    }

    /**
     * Получает место в рейтинге пользователя.
     * @return int|false
     */
    public function getRatingPlacement(): int|false
    {
        $users = User::findAll(['is_executor' => true]);
        uasort($users, function (User $userA, User $userB) {
            return $userB->rating - $userA->rating;
        });

        $result = array_search($this->id, array_column($users, 'id'));

        return $result ? $result++ : 1;
    }

    /**
     * Получает информацию, выполняет ли пользователь задания в данный момент.
     * @return bool
     */
    public function getIsBusy(): bool
    {
        return ! empty(Task::findAll(['executor_id' => $this->id, 'status' => Task::STATUS_ACTIVE]));
    }
}
