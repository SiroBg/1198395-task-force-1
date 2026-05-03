<?php

namespace app\actions;

use app\models\Task;
use app\models\User;
use yii\db\Exception;

class ActionCancel extends ActionAbstract
{
    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'cancel';
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return 'Отменить';
    }

    /**
     * {@inheritDoc}
     */
    public function getButtonColor(): string
    {
        return 'pink';
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
     * {@inheritDoc}
     */
    public function applyAction(
        Task $task,
        User $user,
    ): void {
        try {
            parent::applyAction($task, $user);
            $task->status = $task->getNextStatus($this);
            $task->save();
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}
