<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto me-3 me-lg-4 px-4 px-lg-0 text-center'],
        'items' => [
            ['label' => 'Home', 'url' => ['/site/index']],
            ['label' => 'About', 'url' => ['/site/about']],
            ['label' => 'Contact', 'url' => ['/site/contact']],
            !Yii::$app->user->isGuest? //si esta logueado mostrar lo siguiente
                [
                    'label' => 'Usuario',
                    'items' => [
                        ['label' => 'Usuarios', 'url' => ['/user/index']],
                        ['label' => 'Roles', 'url' => ['/rol/index']],
                        ['label' => 'Asignar roles', 'url' => ['/asignar-rol/index']],
                    ]
                ]:'',
                ['label' => 'resetpassword', 'url' => ['/user/resetpassword']],
                ['label' => 'cambiarpassword', 'url' => ['/user/changepassword']],
            Yii::$app->user->isGuest
                ? ['label' => 'Login', 'url' => ['/site/login']]
                : '<li class="nav-item">'
                    . Html::beginForm(['/site/logout'])
                    . Html::submitButton(
                        'Salir (' . Yii::$app->session->get('nombres') . ')',
                        ['class' => 'nav-link btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>'
        ]
    ]);
    NavBar::end();
    ?>
</header>


<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <div class="position-relative">
        <div class="position-absolute top-0 end-0">
            <?php 
            //get sesion role
            
                $role = Yii::$app->cache->get('RolesOlyNameList_' . Yii::$app->user->id);
               
                if($role){
                    $index = 0;
                    foreach ($role as $roleName) {
                        if ($role[$index] == 'SuperAdmin')
                            echo '<span class="badge rounded-pill bg-danger ml-2">' . $roleName . '</span><br>';
                        else if ($role[$index] == 'profesor')
                            echo '<span class="badge rounded-pill bg-success text-white ml-2">' . $roleName . '</span><br>';
                        else
                            echo '<span class="badge rounded-pill bg-info text-white ml-2">' . $roleName. '</span><br>';
                        $index = $index + 1;
                            }
                 }
            ?>
        </div>
        </div>

        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; My Company <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
