<?php

namespace app\components;

use yii\base\Widget;
use yii\data\ActiveDataProvider;

class TaskWidget extends Widget
{
    public ActiveDataProvider $provider;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        return $this->render('tasks', [
            'provider' => $this->provider,
        ]);
    }
}