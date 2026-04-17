<?php

namespace app\actions;

abstract class actionAbstract
{
    abstract public static function getName(): string;

    abstract public static function getDescription(): string;

    abstract public static function getButtonColor(): string;

    abstract public static function checkRights(
        int $executorId,
        int $authorId,
        int $userId,
        bool $isExecutor,
    ): bool;
}
