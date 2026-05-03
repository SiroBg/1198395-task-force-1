<?php

namespace app\actions;

use app\exceptions\ActionRightsException;
use app\exceptions\TaskStatusException;
use app\models\Respond;
use app\models\Task;
use app\models\User;
use Yii;
use yii\db\Exception;

class ActionRespond extends ActionAbstract
{
    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'act_response';
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return 'Откликнуться на задание';
    }

    /**
     * {@inheritDoc}
     */
    public function getButtonColor(): string
    {
        return 'blue';
    }

    /**
     * {@inheritDoc}
     */
    public function checkRights(
        ?int $executorId,
        int  $authorId,
        int  $userId,
        bool $isExecutor,
    ): bool {
        return $isExecutor && is_null($executorId) && $userId !== $authorId;
    }

    /**
     * {@inheritDoc}
     */
    public function applyAction(
        Task $task,
        User $user,
    ): void {
        try {
            parent::applyAction($task, $user);
            $respond = new Respond();

            $respond->task_id     = $task->id;
            $respond->executor_id = $user->id;

            if (Yii::$app->request->getIsPost()) {
                $respond->load(Yii::$app->request->post());

                if ($respond->validate()) {
                    $respond->save();
                }
            }
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}
