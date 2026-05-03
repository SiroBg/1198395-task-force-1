<?php

namespace app\actions;

use app\exceptions\ActionRightsException;
use app\exceptions\TaskStatusException;
use app\models\Task;
use app\models\User;
use yii\db\Exception;
use yii\web\ForbiddenHttpException;
use Yii;

class ActionStart extends ActionAbstract
{
    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'action_start';
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return 'Начать';
    }

    /**
     * {@inheritDoc}
     */
    public function getButtonColor(): string
    {
        return '';
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
        return ! $isExecutor && is_null($executorId) && $userId === $authorId;
    }

    /**
     * @throws Exception
     * @throws TaskStatusException
     * @throws ForbiddenHttpException
     */
    public function applyAction(
        Task $task,
        User $user,
        ?int $executorId = null
    ): void {
        try {
            parent::applyAction($task, $user);
            if ( ! is_null($executorId)) {
                $task->executor_id = $executorId;
                $task->status      = $task->getNextStatus($this);

                $task->save();
            }
        } catch (ActionRightsException $e) {
            throw new Exception($e->getMessage());
        }
    }
}
