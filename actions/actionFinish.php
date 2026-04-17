<?php

namespace app\actions;

class actionFinish extends actionAbstract
{
    public static function getName(): string
    {
        return 'completion';
    }

    public static function getDescription(): string
    {
        return 'Завершить задание';
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
        return !$isExecutor && $userId === $authorId && $userId !== $executorId;
    }
}
