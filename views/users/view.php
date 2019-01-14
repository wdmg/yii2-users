<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model wdmg\users\models\Users */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/users', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ucfirst($this->title);
\yii\web\YiiAsset::register($this);
?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="users-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'auth_key',
            'password_hash',
            'password_reset_token',
            'email:email',
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function($data) {

                    if ($data->status == $data::USR_STATUS_DELETED)
                        return '<span class="label label-danger">'.Yii::t('app/modules/users','Deleted').'</span>';
                    elseif ($data->status == $data::USR_STATUS_ACTIVE)
                        return '<span class="label label-success">'.Yii::t('app/modules/users','Active').'</span>';
                    else
                        return false;

                },
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
        ],
    ]) ?>

    <p>
        <?= Html::a(Yii::t('app/modules/users', '&larr; Back to list'), ['tasks/index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a(Yii::t('app/modules/users', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app/modules/users', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => Yii::t('app/modules/users', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

</div>