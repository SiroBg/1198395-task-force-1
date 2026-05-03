<?php

namespace app\actions;

use app\exceptions\TaskStatusException;
use app\models\Task;
use app\models\User;
use app\exceptions\ActionRightsException;
use yii\db\Exception;

abstract class ActionAbstract
{
    /**
     * Возвращает имя действия.
     * @return string
     */
    abstract public function getName(): string;

    /**
     * Возвращает описание действия.
     * @return string
     */
    abstract public function getDescription(): string;

    /**
     * Возвращает цвет для кнопки действия.
     * @return string
     */
    abstract public function getButtonColor(): string;

    /**
     * Проверяет права для применения действия.
     * @param  int   $executorId  ID исполнителя задания.
     * @param  int   $authorId    ID автора задания.
     * @param  int   $userId      ID пользователя, применяюещего действие.
     * @param  bool  $isExecutor  Является ли пользователь исполнителем.
     * @return bool
     */
    abstract public function checkRights(
        int  $executorId,
        int  $authorId,
        int  $userId,
        bool $isExecutor,
    ): bool;

    /**
     * Применяет действие к переданному заданию.
     * @param  Task  $task  Задание, к которому будет применено действие.
     * @param  User  $user  Пользователь, применяющий действие.
     * @throws ActionRightsException
     * @throws TaskStatusException
     * @throws Exception
     */
    public function applyAction(
        Task $task,
        User $user,
    ): void {
        try {
            $taskActionsNames = array_map(
                function ($action) {
                    return $action->getName();
                },
                $task->getActions(),
            );
            if (
                ! in_array($this->getName(), $taskActionsNames)
                || ! $this->checkRights(
                    $task->executor_id,
                    $task->author_id,
                    $user->id,
                    $user->is_executor
                )
            ) {
                throw new ActionRightsException(
                    'Нет прав для выполнения действия '
                    .$this->getDescription()
                );
            }
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}
