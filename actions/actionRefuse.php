<?php

namespace app\actions;

use app\exceptions\TaskStatusException;
use app\models\Task;
use app\models\User;
use yii\db\Exception;
use yii\web\ForbiddenHttpException;

class actionRefuse extends actionAbstract
{
    public static function getName(): string
    {
        return 'refusal';
    }

    public static function getDescription(): string
    {
        return 'Отказаться от задания';
    }

    public static function getButtonColor(): string
    {
        return 'orange';
    }

    public static function checkRights(
        ?int $executorId,
        int $authorId,
        int $userId,
        bool $isExecutor,
    ): bool {
        return $isExecutor && $userId === $executorId && $userId !== $authorId;
    }

    /**
     * @throws Exception
     * @throws TaskStatusException
     * @throws ForbiddenHttpException
     */
    public static function execute(Task $task, User $user): bool
    {
        if (!$task->applyAction(
            new actionRefuse(),
            $user->id,
            $user->is_executor,
        )
        ) {
            throw new ForbiddenHttpException(
                'Невозможно отказаться от задания',
            );
        }

        return $task->save(false);
    }
}
