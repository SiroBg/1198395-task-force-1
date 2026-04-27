<?php

namespace app\actions;

use app\exceptions\TaskStatusException;
use app\models\Review;
use app\models\Task;
use app\models\User;
use Yii;

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

    /**
     * @throws TaskStatusException
     */
    public static function execute(Task $task, User $user): bool
    {
        $result = false;

        $review = new Review();

        $review->task_id = $task->id;
        $review->author_id = $task->author_id;
        $review->executor_id = $task->executor_id;

        if (Yii::$app->request->getIsPost()) {
            $review->load(Yii::$app->request->post());
            if ($review->validate()
                && $task->applyAction(
                    new actionFinish(),
                    $user->id,
                    $user->is_executor,
                )
            ) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if (!$task->save(false) || !$review->save()) {
                        throw new \Exception(
                            'Ошибка при сохранении данных на сервере.',
                        );
                    }

                    $transaction->commit();
                    $result = true;
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage());
                }
            }
        }

        return $result;
    }
}
