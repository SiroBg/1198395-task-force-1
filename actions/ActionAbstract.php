<?php

namespace app\actions;

use app\exceptions\TaskStatusException;
use app\models\Task;
use app\models\User;
use app\exceptions\ActionRightsException;
use yii\db\Exception;

abstract class ActionAbstract
{
    abstract public function getName(): string;

    abstract public function getDescription(): string;

    abstract public function getButtonColor(): string;

    abstract public function checkRights(
        int $executorId,
        int $authorId,
        int $userId,
        bool $isExecutor,
    ): bool;

    /**
     * @throws ActionRightsException
     * @throws TaskStatusException
     */
    public function applyAction(
        Task $task,
        User $user,
    ): void {
        try {
            $taskActionsNames = array_map(
                function ($action) {
                    return $action->getName();
                },
                $task->getActions(),
            );
            if (
                !in_array($this->getName(), $taskActionsNames)
                || !$this->checkRights(
                    $task->executor_id,
                    $task->author_id,
                    $user->id,
                    $user->is_executor
                )
            ) {
                throw new ActionRightsException(
                    'Нет прав для выполнения действия '
                    . $this->getDescription()
                );
            }
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}
