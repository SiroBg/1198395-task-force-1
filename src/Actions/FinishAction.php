<?php

namespace TaskForce\Actions;

class FinishAction extends AbstractAction
{
    public function getName(): string
    {
        return 'completion';
    }

    public function getDescription(): string
    {
        return 'Завершить задание';
    }

    public function getButtonColor(): string
    {
        return 'pink';
    }

    public function checkRights(
        ?int $executorId,
        int $authorId,
        int $userId,
    ): bool {
        return $userId === $authorId && $userId !== $executorId;
    }
}
