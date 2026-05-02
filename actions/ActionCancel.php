<?php

namespace app\actions;

use app\models\Task;
use app\models\User;
use yii\db\Exception;

class ActionCancel extends ActionAbstract
{
    public function getName(): string
    {
        return 'cancel';
    }

    public function getDescription(): string
    {
        return 'Отменить';
    }

    public function getButtonColor(): string
    {
        return 'pink';
    }

    public function checkRights(
        ?int $executorId,
        int $authorId,
        int $userId,
        bool $isExecutor,
    ): bool {
        return !$isExecutor && is_null($executorId) && $userId === $authorId;
    }

    /**
     * @throws Exception
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
