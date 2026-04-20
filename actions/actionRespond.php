<?php

namespace app\actions;

class actionRespond extends actionAbstract
{
    public static function getName(): string
    {
        return 'act_response';
    }

    public static function getDescription(): string
    {
        return 'Откликнуться на задание';
    }

    public static function getButtonColor(): string
    {
        return 'blue';
    }

    public static function checkRights(
        ?int $executorId,
        int $authorId,
        int $userId,
        bool $isExecutor,
    ): bool {
        return $isExecutor && is_null($executorId) && $userId !== $authorId;
    }

    public static function execute($task, $user, $respond)
    {
        $result = false;
        if (Yii::$app->request->getIsPost()) {
            $respond->load(Yii::$app->request->post());

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($respond);
            }

            if ($respond->validate()
                && $task->applyAction(
                    new actionRespond(),
                    $user->id,
                    $user->is_executor,
                )
            ) {
                if (!$respond->save()) {
                    throw new \Exception(
                        'Ошибка при сохранении данных на сервере.',
                    );
                }

                $result = true;
            }
        }

        return $result;
    }
}
