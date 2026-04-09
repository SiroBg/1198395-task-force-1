<?php

namespace TaskForce\Actions;

class RespondAction extends AbstractAction
{
    public function getName(): string
    {
        return 'act_response';
    }

    public function getDescription(): string
    {
        return 'Откликнуться на задание';
    }

    public function getButtonColor(): string
    {
        return 'blue';
    }

    public function checkRights(
        ?int $executorId,
        int $authorId,
        int $userId,
    ): bool {
        return is_null($executorId) && $userId !== $authorId;
    }
}
