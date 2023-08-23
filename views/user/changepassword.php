<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Cambiar Contraseña';
$this->params['breadcrumbs'][] = $this->title;

// yii flash error

?>

<div class="user-change-password">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Por favor, complete los siguientes campos para cambiar su contraseña:</p>



    <?php $form = ActiveForm::begin(['id' => 'change-password-form']); ?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'username')->textInput(['autofocus' => true])?>

    <?= $form->field($model, 'currentPassword')->passwordInput()->label('Contraseña actual')->error() ?>

    <?= $form->field($model, 'newPassword')->passwordInput()->label('Nueva contraseña')->error()  ?>

    <?= $form->field($model, 'confirmPassword')->passwordInput()->label('Confirmar nueva contraseña')->error()  ?>

    <div class="form-group">
        <?= Html::submitButton('Cambiar Contraseña', ['class' => 'btn btn-primary', 'name' => 'change-password-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
