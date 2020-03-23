<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model wdmg\users\models\Users */

$this->title = Yii::t('app/modules/users', 'Update user: {name}', [
    'name' => $model->username,
]);
$this->params['breadcrumbs'][] = ['label' => $this->context->module->name, 'url' => ['users/index']];
$this->params['breadcrumbs'][] = ['label' => ucfirst($model->username), 'url' => ['users/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/modules/users', 'Update');
?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="users-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
