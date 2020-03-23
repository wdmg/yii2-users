<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $model wdmg\users\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'auth_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password_hash')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->widget(SelectInput::class, [
        'items' => $model->getStatusesList(false),
        'options' => [
            'class' => 'form-control'
        ]
    ]); ?>

    <hr/>
    <div class="form-group">
        <?= Html::a(Yii::t('app/modules/users', '&larr; Back to list'), ['users/index'], ['class' => 'btn btn-default pull-left']) ?>&nbsp;
        <?= Html::submitButton(Yii::t('app/modules/users', 'Save'), ['class' => 'btn btn-success pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
