<?php

namespace app\actions;

use app\exceptions\TaskStatusException;
use app\models\Task;
use app\models\User;
use yii\db\Exception;
use yii\web\ForbiddenHttpException;

class actionStart extends actionAbstract
{
    public static function getName(): string
    {
        return 'action_start';
    }

    public static function getDescription(): string
    {
        return 'Начать';
    }

    public static function getButtonColor(): string
    {
        return '';
    }

    public static function checkRights(
        ?int $executorId,
        int $authorId,
        int $userId,
        bool $isExecutor,
    ): bool {
        return !$isExecutor && is_null($executorId) && $userId === $authorId;
    }

    /**
     * @throws Exception
     * @throws TaskStatusException
     * @throws ForbiddenHttpException
     */
    public static function execute(
        Task $task,
        User $user,
        int $executorId
    ): bool {
        if (!$task->applyAction(
            new actionStart(),
            $user->id,
            $user->is_executor,
            $executorId,
        )
        ) {
            throw new ForbiddenHttpException(
                'Невозможно назначить исполнителя задания',
            );
        }

        return $task->save();
    }
}
