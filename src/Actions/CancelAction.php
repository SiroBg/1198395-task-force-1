<?php

namespace TaskForce\Actions;

class CancelAction extends AbstractAction
{
    public function getName(): string
    {
        return 'cancel';
    }

    public function getDescription(): string
    {
        return 'Отменить';
    }

    public function getButtonColor(): string
    {
        return 'pink';
    }

    public function checkRights(
        ?int $executorId,
        int $authorId,
        int $userId,
        bool $isExecutor
    ): bool {
        return !$isExecutor && is_null($executorId) && $userId === $authorId;
    }
}
