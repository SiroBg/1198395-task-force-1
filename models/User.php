<?php

namespace app\models;

use DateInterval;
use DateTime;
use Yii;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

/**
 * This is the model class for table "users".
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
 * @property Task[]         $tasks0
 * @property UserCategory[] $userCategories
 * @property float          $rating
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    public string                   $password_retype = '';
    public array|string             $categories      = [];
    public null|UploadedFile|string $avatar          = null;

    public static function findIdentity($id): User|IdentityInterface|null
    {
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
    }

    public function validateAuthKey($authKey)
    {
    }

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
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity(): \yii\db\ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[ProfileImgFile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfileImgFile(): \yii\db\ActiveQuery
    {
        return $this->hasOne(File::class, ['id' => 'profile_img_file_id']);
    }

    /**
     * Gets query for [[Responds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponds(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Respond::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Review::class, ['author_id' => 'id']);
    }

    /**
     * Gets query for [[ReviewsAsExecutor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviewsAsExecutor(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Review::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Task::class, ['author_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks0(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Task::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[UserCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserCategories(): \yii\db\ActiveQuery
    {
        return $this->hasMany(UserCategory::class, ['user_id' => 'id']);
    }

    public function getName()
    {
        if ($id = Yii::$app->user->getId()) {
            return self::findOne($id);
        }
    }

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
}
