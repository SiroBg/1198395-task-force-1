<?php

namespace app\actions;

use app\exceptions\ActionRightsException;
use app\exceptions\TaskStatusException;
use app\models\Task;
use app\models\User;
use yii\db\Exception;
use yii\web\ForbiddenHttpException;
use Yii;

class ActionRefuse extends ActionAbstract
{
    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'refusal';
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return 'Отказаться от задания';
    }

    /**
     * {@inheritDoc}
     */
    public function getButtonColor(): string
    {
        return 'orange';
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
        return $isExecutor && $userId === $executorId && $userId !== $authorId;
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
