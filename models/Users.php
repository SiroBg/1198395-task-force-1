<?php

namespace app\models;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string|null $created_at
 * @property string $email
 * @property string $name
 * @property int $city_id
 * @property string $password
 * @property int $is_executor
 * @property int|null $profile_img_file_id
 * @property string|null $birthday
 * @property string|null $phone
 * @property string|null $telegram
 * @property string|null $about
 *
 * @property Cities $city
 * @property Files $profileImgFile
 * @property Responds[] $responds
 * @property Reviews[] $reviews
 * @property Reviews[] $reviews0
 * @property Tasks[] $tasks
 * @property Tasks[] $tasks0
 * @property UserCategories[] $userCategories
 */
class Users extends \yii\db\ActiveRecord
{
    public string $password_retype = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['profile_img_file_id', 'birthday', 'phone', 'telegram', 'about'], 'default', 'value' => null],
            [['created_at', 'birthday'], 'safe'],
            [['email', 'name', 'city_id', 'password', 'password_retype', 'is_executor'], 'required'],
            [['city_id', 'is_executor', 'profile_img_file_id'], 'integer'],
            [['about'], 'string'],
            [['email', 'name'], 'string', 'max' => 256],
            [['password'], 'string', 'min' => 8, 'max' => 128],
            ['password_retype', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают'],
            [['phone'], 'string', 'max' => 11],
            [['telegram'], 'string', 'max' => 64],
            [['email'], 'unique'],
            [['profile_img_file_id'], 'exist', 'skipOnError' => true, 'targetClass' => Files::class, 'targetAttribute' => ['profile_img_file_id' => 'id']],
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
            'email' => 'Email',
            'name' => 'Ваше имя',
            'city_id' => 'Город',
            'password' => 'Пароль',
            'password_retype' => 'Повтор пароля',
            'is_executor' => 'я собираюсь откликаться на заказы',
            'profile_img_file_id' => 'Profile Img File ID',
            'birthday' => 'Birthday',
            'phone' => 'Phone',
            'telegram' => 'Telegram',
            'about' => 'About',
        ];
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
     * Gets query for [[ProfileImgFile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfileImgFile()
    {
        return $this->hasOne(Files::class, ['id' => 'profile_img_file_id']);
    }

    /**
     * Gets query for [[Responds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponds()
    {
        return $this->hasMany(Responds::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Reviews::class, ['author_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews0()
    {
        return $this->hasMany(Reviews::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Tasks::class, ['author_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks0()
    {
        return $this->hasMany(Tasks::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[UserCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserCategories()
    {
        return $this->hasMany(UserCategories::class, ['user_id' => 'id']);
    }

}
