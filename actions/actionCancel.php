<?php

namespace app\actions;

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
}
