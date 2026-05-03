<?php

namespace app\models;

use DateInterval;
use DateTime;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

class TaskSearch extends Model
{
    public array|string $categories = [];
    public bool         $noResponds = false;
    public bool         $remoteTask = false;
    public string       $period     = '';

    public const array PERIODS_OPTIONS
        = [
            ''      => 'Любой',
            'PT1H'  => '1 час',
            'PT12H' => '12 часов',
            'PT24H' => '24 часа',
        ];

    private array $allowedTaskStatuses
        = [
            'new',
            'active',
            'expired',
            'closed',
        ];

    /**
     * {@inheritDoc}
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'categories' => 'Категории',
            'noResponds' => 'Без откликов',
            'remoteTask' => 'Удалённая работа',
            'period'     => 'Период',
        ];
    }

    /**
     * {@inheritDoc}
     * @return array
     */
    public function rules(): array
    {
        return [
            ['categories', 'each', 'rule' => ['integer']],
            [['noResponds', 'remoteTask'], 'boolean'],
            ['period', 'validateIsoDuration'],
        ];
    }

    /**
     * Получает отфильтрованный провайдер.
     * @param  int  $userCityId  Город пользователя для фильтрации по городам.
     * @return ActiveDataProvider
     * @throws \DateMalformedIntervalStringException
     */
    public function getFilteredProvider(int $userCityId): ActiveDataProvider
    {
        $provider = $this->getNewTasksProvider($userCityId);

        if ( ! empty($this->categories)) {
            $provider->query->andWhere(['category_id' => $this->categories]);
        }

        if ($this->noResponds) {
            $provider->query->andWhere(
                [
                    'not exists',
                    (new Query()->select(['task_id'])->from('responds')
                                ->where('responds.task_id = tasks.id'))
                ]
            );
        }

        if ($this->remoteTask) {
            $provider->query->andWhere(['location' => '']);
        }

        if ( ! empty($this->period) && $this->validate()) {
            $interval = new DateInterval($this->period);

            $date = date_sub(new DateTime(), $interval);
            $provider->query->andWhere(
                ['>', 'created_at', $date->format('Y-m-d H:i:s')],
            );
        }
        return $provider;
    }

    /**
     * Получает провайдер без фильтров формы.
     * @param  int  $userCityId  Город пользователя.
     * @return ActiveDataProvider
     */
    public function getNewTasksProvider(int $userCityId): ActiveDataProvider
    {
        $provider = $this->initProvider();
        $provider->query->andWhere(['status' => Task::STATUS_NEW, 'city_id' => [$userCityId, null]]);
        return $provider;
    }

    /**
     * Получает провайдер с заданиями для страницы "мои задания".
     * @param  User         $user    Информация о пользователе.
     * @param  string|null  $status  Статус для фильтрации заданий.
     * @return ActiveDataProvider
     */
    public function getUsersTasksProvider(User $user, ?string $status): ActiveDataProvider
    {
        $provider = $this->initProvider();
        if ( ! in_array($status, $this->allowedTaskStatuses)) {
            $status = $user->is_executor ? 'active' : 'new';
        }

        if ( ! $user->is_executor) {
            $provider->query->where(['author_id' => $user->id]);
        } else {
            $provider->query->where(['executor_id' => $user->id]);
        }
        switch ($status) {
            case 'active':
                $provider->query->andWhere(['status' => Task::STATUS_ACTIVE]);
                break;
            case 'closed':
                $provider->query->andWhere([
                    'status' => [
                        Task::STATUS_FINISHED,
                        Task::STATUS_FAILED,
                        Task::STATUS_CANCELED,
                    ],
                ]);
                break;
            case 'expired':
                $provider->query->andWhere(['status' => Task::STATUS_ACTIVE])
                                ->andWhere(['<', 'expire_date', new Expression('CURDATE()'),]);
                break;
            case 'new':
                $provider->query->andWhere(['status' => Task::STATUS_NEW]);
                break;
        }
        return $provider;
    }

    /**
     * Создаёт изначальный провайдер.
     * @return ActiveDataProvider
     */
    private function initProvider(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query'      => Task::find(),
            'pagination' => [
                'pageSize' => 5,
            ],
            'sort'       => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);
    }

    /**
     * Валидирует фильтр поиска по периоду.
     * @param $attribute
     * @return void
     */
    public function validateIsoDuration($attribute): void
    {
        if ( ! empty($this->$attribute)) {
            try {
                new DateInterval($this->$attribute);
            } catch (\Exception $e) {
                $this->addError($attribute, 'Выберите период из списка.');
            }
        }
    }

}
