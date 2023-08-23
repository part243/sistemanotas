<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/** @var app\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->registerJs("
$('.delete-button').on('click', function() {
    SweetAlert.confirm({
        title: 'Are you sure?',
        text: 'This action cannot be undone.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
    }).then(function() {
        // Delete the record
    }, function() {
        // Do nothing
    });
});
");

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            //'password',
            //'auth_key',
            [
                'attribute' => 'status',
                'value' => function (User $model) {
                    return $model->status === User::STATUS_ACTIVE ? 'Active' : 'Inactive';
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'status',
                    [
                        User::STATUS_ACTIVE => 'Active',
                        User::STATUS_INACTIVE => 'Inactive',
                    ],
                    ['class' => 'form-control', 'prompt' => 'All']
                ),

            ],
            
            'created_at',
            //'updated_at',
            [
                'class' => ActionColumn::className(),
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    
                    'delete' => function ($url, $model, $key) {
                        return Html::a(
                            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                <path d="M5.5 1a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM3.78 4H12.5a1.5 1.5 0 0 1 1.5 1.5V14a1.5 1.5 0 0 1-1.5 1.5H3.78a.5.5 0 0 1-.47-.34L2 9.41l-.29.3a.5.5 0 0 1-.71 0l-.3-.3-1.29-1.3a.5.5 0 0 1 0-.71l1.29-1.3.3-.3a.5.5 0 0 1 .7 0l1.3 1.29 1.3-1.29a.5.5 0 0 1 .71 0l.3.3 1.29 1.3a.5.5 0 0 1 0 .71l-1.29 1.3-.3.3a.5.5 0 0 1-.7 0l-1.3-1.29-1.3 1.29a.5.5 0 0 1-.71 0l-.3-.3-1.29-1.3a.5.5 0 0 1 0-.71l1.29-1.3.3-.3a.5.5 0 0 1 .48-.04zM14 4.5V14a.5.5 0 0 1-.5.5H3.78a.5.5 0 0 1-.35-.15l-1.5-1.5a.5.5 0 0 1-.15-.35V5a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5z"/>
                            </svg>',
                            ['delete', 'id' => $model->id],
                            [
                                'class' => 'delete-button',
                                'data-confirm' => '¿Estás seguro de que deseas eliminar este elemento?', // Mensaje de confirmación
                                'data-method' => 'post',
                            ]
                        );
                    },
                ],

                'urlCreator' => function ($action, User $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
