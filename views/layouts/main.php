<?php
/** @var yii\web\View $this */

/**
 * @var string $content
 * @var User   $user
 */

use app\assets\AppAsset;
use app\models\User;
use yii\bootstrap5\Html;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(
    ['name' => 'viewport', 'content' => 'width=device-width,initial-scale=1'],
);
$this->registerMetaTag(
    [
        'name'    => 'description',
        'content' => $this->params['meta_description'] ?? '',
    ],
);
$this->registerMetaTag(
    ['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? ''],
);
$this->registerLinkTag(
    [
        'rel'  => 'icon',
        'type' => 'image/x-icon',
        'href' => Yii::getAlias('@web/favicon.ico'),
    ],
);
$user = User::findOne(Yii::$app->user->id);
?>
<?php
$this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php
    $this->head() ?>
</head>
<body>
<?php
$this->beginBody() ?>

<header class="page-header">
    <nav class="main-nav">
        <a href='/' class="header-logo">
            <img class="logo-image" src="/img/logotype.png" width=227 height=60
                 alt="taskforce">
        </a>
        <?php
        if (Url::current() === '/sign-up/index'): ?>
    </nav>
    <?php
    else : ?>
        <div class="nav-wrapper">
            <ul class="nav-list">
                <li class="list-item <?= Url::current() === '/tasks/index' ? 'list-item--active' : '' ?>">
                    <a href="/" class="link link--nav">Новое</a>
                </li>
                <li class="list-item <?= Url::current() === '/my-tasks/index' ? 'list-item--active' : '' ?>">
                    <a href="/my-tasks/" class="link link--nav">Мои задания</a>
                </li>
                <?php
                if ( ! $user->is_executor) : ?>
                    <li class="list-item <?= Url::current() === '/add-task/index' ? 'list-item--active' : '' ?>">
                        <a href="/add-task" class="link link--nav">Создать
                            задание</a>
                    </li>
                <?php
                endif; ?>
                <li class="list-item <?= Url::current() === '/user-settings/index' ? 'list-item--active' : '' ?>">
                    <a href="/user-settings" class="link link--nav">Настройки</a>
                </li>
            </ul>
        </div>
        </nav>
        <div class="user-block">
            <a href="<?= Url::to('/users/view/'.$user->id) ?>">
                <img class="user-photo" src="<?= $user->profileImgFile->url ?? '/img/avatar-placeholder.png' ?>"
                     width="55"
                     height="55" alt="Аватар">
            </a>
            <div class="user-menu">
                <p class="user-name"><?= htmlspecialchars(
                        $user->name,
                    ); ?></p>
                <div class="popup-head">
                    <ul class="popup-menu">
                        <li class="menu-item">
                            <?= Html::a('Настройки', ['/user-settings/'], ['class' => 'link']) ?>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="link">Связаться с нами</a>
                        </li>
                        <li class="menu-item">
                            <?= Html::a('Выход из системы', ['/users/logout/'], ['class' => 'link']) ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    <?php
    endif; ?>
</header>

<?= $content ?>

<?php
$this->endBody() ?>
</body>
</html>
<?php
$this->endPage() ?>
