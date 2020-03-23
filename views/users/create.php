<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model wdmg\users\models\Users */

$this->title = Yii::t('app/modules/users', 'Create user');
$this->params['breadcrumbs'][] = ['label' => $this->context->module->name, 'url' => ['users/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="users-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
