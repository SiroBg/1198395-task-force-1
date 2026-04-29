<?php

namespace app\actions;

use app\exceptions\ActionRightsException;
use app\models\Review;
use app\models\Task;
use app\models\User;
use Yii;
use yii\db\Exception;

class ActionFinish extends ActionAbstract
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
        bool $isExecutor,
    ): bool {
        return !$isExecutor && $userId === $authorId && $userId !== $executorId;
    }

    /**
     * @throws Exception
     */
    public function applyAction(
        Task $task,
        User $user,
    ): void {
        try {
            parent::applyAction($task, $user);
        } catch (ActionRightsException $e) {
            throw new Exception($e->getMessage());
        }

        $review = new Review();

        $review->task_id = $task->id;
        $review->author_id = $task->author_id;
        $review->executor_id = $task->executor_id;

        if (Yii::$app->request->getIsPost()) {
            $review->load(Yii::$app->request->post());
            if ($review->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $task->status = $task->getNextStatus($this);
                    if ($task->save() && $review->save()) {
                        $transaction->commit();
                    }
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    throw new Exception($e->getMessage());
                }
            }
        }
    }
}
