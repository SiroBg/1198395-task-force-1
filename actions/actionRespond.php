<?php

namespace app\actions;

class actionRespond extends actionAbstract
{
    public static function getName(): string
    {
        return 'act_response';
    }

    public static function getDescription(): string
    {
        return 'Откликнуться на задание';
    }

    public static function getButtonColor(): string
    {
        return 'blue';
    }

    public static function checkRights(
        ?int $executorId,
        int $authorId,
        int $userId,
        bool $isExecutor,
    ): bool {
        return $isExecutor && is_null($executorId) && $userId !== $authorId;
    }
}
