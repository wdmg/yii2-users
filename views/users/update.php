<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model wdmg\users\models\Users */

$this->title = Yii::t('app/modules/users', 'Update user: {name}', [
    'name' => $model->username,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/users', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => ucfirst($model->username), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/modules/users', 'Update');
?>
<div class="users-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
