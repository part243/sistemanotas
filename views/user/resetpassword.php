<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Resetear contraseña';
$this->params['breadcrumbs'][] = $this->title;

// yii flash error

?>

<div class="user-reset-password">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Por favor, complete los siguientes campos para resetear contraseña:</p>



    <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Ingrese el nombre de usuario al que desea resetear la contraseña')?>

    <div class="form-group">
        <?= Html::submitButton('Resetear contraseña', ['class' => 'btn btn-primary', 'name' => 'change-password-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
