<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use wdmg\widgets\SelectInput;
/* @var $this yii\web\View */
/* @var $searchModel wdmg\users\models\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $this->context->module->name;
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
            [
                'attribute' => 'username',
                'format' => 'html',
                'value' => function($data) {

                    if ($data->is_online)
                        $badge = '<span class="fa fa-circle text-success" data-rel="tooltip" title="' . Yii::t('app/modules/users', 'User is online') . '"></span>';
                    else
                        $badge = '<span class="fa fa-circle text-muted" data-rel="tooltip" title="' . Yii::t('app/modules/users', 'Last seen {last_seen}', [
                            'last_seen' => Yii::$app->formatter->asDatetime($data->lastseen_at)
                        ]) . '"></span>';

                    return $data->username .'&nbsp;'. $badge;
                }
            ],
            'email:email',
            (Yii::$app->getAuthManager()) ? [
                'attribute' => 'role',
                'format' => 'html',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'role',
                    'items' => $searchModel->getRolesList(true),
                    'options' => [
                        'id' => 'roleFilter',
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) {
                    $role = $data->getDefaultRole(true);
                    return Html::a($role->name, ['../rbac/roles/view', 'id' => $role->name]);
                }
            ] : ['visible' => false],
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
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'status',
                    'items' => $searchModel->getStatusesList(true),
                    'options' => [
                        'id' => 'statusFilter',
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) {
                    if ($data->status == $data::USR_STATUS_BLOCKED)
                        return '<span class="label label-danger">'.Yii::t('app/modules/users','Blocked').'</span>';
                    elseif ($data->status == $data::USR_STATUS_ACTIVE)
                        return '<span class="label label-success">'.Yii::t('app/modules/users','Active').'</span>';
                    elseif ($data->status == $data::USR_STATUS_DELETED)
                        return '<span class="label label-default">'.Yii::t('app/modules/users','Deleted').'</span>';
                    elseif ($data->status == $data::USR_STATUS_WAITING)
                        return '<span class="label label-warning">'.Yii::t('app/modules/users','Waiting').'</span>';
                    else
                        return false;
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
        'pager' => [
            'options' => [
                'class' => 'pagination',
            ],
            'maxButtonCount' => 5,
            'activePageCssClass' => 'active',
            'linkContainerOptions' => [
                'class' => 'linkContainerOptions',
            ],
            'linkOptions' => [
                'class' => 'linkOptions',
            ],
            'prevPageCssClass' => '',
            'nextPageCssClass' => '',
            'firstPageCssClass' => 'previous',
            'lastPageCssClass' => 'next',
            'firstPageLabel' => Yii::t('app/modules/users', 'First page'),
            'lastPageLabel'  => Yii::t('app/modules/users', 'Last page'),
            'prevPageLabel'  => Yii::t('app/modules/users', '&larr; Prev page'),
            'nextPageLabel'  => Yii::t('app/modules/users', 'Next page &rarr;')
        ],
    ]); ?>
    <div>
        <?= Html::a(Yii::t('app/modules/users', 'Add new user'), ['create'], ['class' => 'btn btn-success pull-right']) ?>
    </div>
<?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>