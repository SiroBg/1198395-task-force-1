<?php

namespace app\actions;

use app\models\Tasks;
use app\models\Users;

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

    public static function execute(Tasks $task, Users $user)
    {
        if (!$task->applyAction(
            new RefuseAction(),
            $user->id,
            $user->is_executor,
        )
        ) {
            throw new ForbiddenHttpException(
                'Невозможно отказаться от задания',
            );
        }

        Tasks::updateAll(
            ['status' => $task->status],
            ['id' => $task->id],
        );
    }
}
