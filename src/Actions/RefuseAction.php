<?php

namespace TaskForce\Actions;

class RefuseAction extends AbstractAction
{
    public function getName(): string
    {
        return 'refusal';
    }

    public function getDescription(): string
    {
        return 'Отказаться от задания';
    }

    public function getButtonColor(): string
    {
        return 'orange';
    }

    public function checkRights(
        ?int $executorId,
        int $authorId,
        int $userId,
    ): bool {
        return $userId === $executorId && $userId !== $authorId;
    }
}
