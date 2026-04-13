<?php

namespace TaskForce\Actions;

class StartAction extends AbstractAction
{
    public function getName(): string
    {
        return 'action_start';
    }

    public function getDescription(): string
    {
        return 'Начать';
    }

    public function getButtonColor(): string
    {
        return '';
    }

    public function checkRights(
        ?int $executorId,
        int $authorId,
        int $userId,
        bool $isExecutor,
    ): bool {
        return !$isExecutor && is_null($executorId) && $userId === $authorId;
    }
}
