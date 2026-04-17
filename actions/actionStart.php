<?php

namespace app\actions;

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
}
