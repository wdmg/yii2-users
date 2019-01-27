<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use wdmg\helpers\DateAndTime;

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
            [
                'attribute' => 'auth_key',
                'format' => 'html',
                'value' => function($data) {
                    $string = $data->auth_key;
                    $length = strlen($string);
                    $sub_len = abs($length / 10);
                    if($string && $length > 6)
                        return substr($string, 0, $sub_len) . '…' . substr($string, -$sub_len, $sub_len) . ' <span class="text-muted pull-right">[length: '.$length.']</span>';
                    else
                        return $string;
                }
            ],
            [
                'attribute' => 'password_hash',
                'format' => 'html',
                'value' => function($data) {
                    $string = $data->password_hash;
                    $length = strlen($string);
                    $sub_len = abs($length / 10);
                    if($string && $length > 6)
                        return substr($string, 0, $sub_len) . '…' . substr($string, -$sub_len, $sub_len) . ' <span class="text-muted pull-right">[length: '.$length.']</span>';
                    else
                        return $string;
                }
            ],
            [
                'attribute' => 'password_reset_token',
                'format' => 'html',
                'value' => function($data) {
                    $string = $data->password_reset_token;
                    $length = strlen($string);
                    $sub_len = abs($length / 10);
                    if($string && $length > 6)
                        return substr($string, 0, $sub_len) . '…' . substr($string, -$sub_len, $sub_len) . ' <span class="text-muted pull-right">[length: '.$length.']</span>';
                    else
                        return $string;
                }
            ],
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
                'format' => 'html',
                'value' => function($data) {
                    return \Yii::$app->formatter->asDatetime($data->created_at) . DateAndTime::diff($data->created_at." ", null, [
                            'layout' => '<small class="pull-right {class}">[ {datetime} ]</small>',
                            'inpastClass' => 'text-danger',
                            'futureClass' => 'text-success',
                        ]);
                }
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'html',
                'value' => function($data) {
                    return \Yii::$app->formatter->asDatetime($data->updated_at) . DateAndTime::diff($data->updated_at." ", null, [
                            'layout' => '<small class="pull-right {class}">[ {datetime} ]</small>',
                            'inpastClass' => 'text-danger',
                            'futureClass' => 'text-success',
                        ]);
                }
            ],
        ],
    ]) ?>

    <hr/>
    <div class="form-group">
        <?= Html::a(Yii::t('app/modules/users', '&larr; Back to list'), ['users/index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a(Yii::t('app/modules/users', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app/modules/users', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => Yii::t('app/modules/users', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </div>

</div>