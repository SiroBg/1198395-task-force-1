<?php

namespace app\actions;

use yii\web\ForbiddenHttpException;

class actionCancel extends actionAbstract
{
    public static function getName(): string
    {
        return 'cancel';
    }

    public static function getDescription(): string
    {
        return 'Отменить';
    }

    public static function getButtonColor(): string
    {
        return 'pink';
    }

    public static function checkRights(
        ?int $executorId,
        int $authorId,
        int $userId,
        bool $isExecutor,
    ): bool {
        return !$isExecutor && is_null($executorId) && $userId === $authorId;
    }

    public static function execute($task, $user): bool
    {
        if (!$task->applyAction(
            new actionCancel(),
            $user->id,
            $user->is_executor,
        )
        ) {
            throw new ForbiddenHttpException('Невозможно отменить задание');
        }

        return $task->save();
    }
}
