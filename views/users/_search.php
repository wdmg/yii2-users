<?php

use wdmg\widgets\SelectInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model wdmg\users\models\UsersSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h5 class="panel-title">
            <a data-toggle="collapse" href="#usersSearch">
                <span class="glyphicon glyphicon-search"></span> <?= Yii::t('app/modules/users', 'Users search') ?>
            </a>
        </h5>
    </div>
    <div id="usersSearch" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="users-search">

                <?php $form = ActiveForm::begin([
                    'action' => ['index'],
                    'method' => 'get',
                    'options' => [
                        'data-pjax' => 1
                    ],
                ]); ?>

                <?= $form->field($model, 'id') ?>

                <?= $form->field($model, 'username') ?>

                <?= $form->field($model, 'auth_key') ?>

                <?= $form->field($model, 'password_hash') ?>

                <?= $form->field($model, 'password_reset_token') ?>

                <?= $form->field($model, 'email') ?>

                <?= $form->field($model, 'status')->widget(SelectInput::class, [
                    'items' => $model->getStatusesList(true),
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]); ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app/modules/users', 'Search'), ['class' => 'btn btn-primary']) ?>
                    <?= Html::resetButton(Yii::t('app/modules/users', 'Reset'), ['class' => 'btn btn-default']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
