<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel wdmg\users\models\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/modules/users', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="users-index">

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            'email:email',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) {

                    if ($data->status == $data::USR_STATUS_DELETED)
                        return '<span class="label label-danger">'.Yii::t('app/modules/users','Deleted').'</span>';
                    elseif ($data->status == $data::USR_STATUS_ACTIVE)
                        return '<span class="label label-success">'.Yii::t('app/modules/users','Active').'</span>';
                    else
                        return false;

                },
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <div>
        <!-- ?= Html::a(Yii::t('app/modules/users', '&larr; Back to module'), ['../admin/users'], ['class' => 'btn btn-default pull-left']) ? -->
        <?= Html::a(Yii::t('app/modules/users', 'Add new users'), ['create'], ['class' => 'btn btn-success pull-right']) ?>
    </div>
<?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>