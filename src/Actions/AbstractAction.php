<?php

namespace TaskForce\Actions;

abstract class AbstractAction
{
    abstract public function getName(): string;

    abstract public function getDescription(): string;

    abstract public function getButtonColor(): string;

    abstract public function checkRights(
        int $executorId,
        int $authorId,
        int $userId,
        bool $isExecutor
    ): bool;
}
