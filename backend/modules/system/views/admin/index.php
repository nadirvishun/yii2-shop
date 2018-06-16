<?php

use backend\modules\system\models\Admin;
use kartik\editable\Editable;
use kartik\popover\PopoverX;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\system\models\search\AdminSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Admins');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-index grid-view box box-primary">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'hover' => true,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => '\kartik\grid\CheckboxColumn',
                'rowSelectedClass' => GridView::TYPE_INFO
            ],

            'id',
            'username',
//            'auth_key',
//            'password_hash',
//            'password_reset_token',
            'email:email',
            'mobile',
            // 'avatar',
            // 'sex',
            // 'last_login_ip',
            // 'last_login_time',
            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'status',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => Admin::getStatusOptions(),
                    'options' => [
                        'prompt' => Yii::t('common', 'Please Select...'),
                    ],
                    'hideSearch' => true,
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
                'editableOptions'=>function($model,$key,$index){
                    return [
                        'value' => $model->status,//原始值
                        'displayValueConfig' => Admin::getStatusOptions(),//要显示的文字
                        'header' => $model->getAttributeLabel('status'),
                        'size' => 'md',
                        'placement' => PopoverX::ALIGN_LEFT,//左侧弹出
                        'inputType' => Editable::INPUT_SWITCH,
                        'options' => [
                            'options' => ['uncheck' => 0, 'value' => 1],//switch插件的参数
                            'pluginOptions' => ['size' => 'small'],
                        ],
                    ];
                },
                'value' => function ($model, $key, $index, $column) {
                    return Admin::getStatusOptions($model->status);
                }
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'role',
                'mergeHeader' => true,
                'hAlign' => GridView::ALIGN_CENTER,
                'value' => function ($model, $key, $index, $column) {
                    if ($model->id == Yii::$app->params['superAdminId']) {
                        return Yii::t('common', 'Super Admin');
                    }
                    $auth = Yii::$app->authManager;
                    $roles = $auth->getRolesByUser($model->id);
                    return empty($roles) ? Yii::t('admin', 'No Role') : implode('，', array_keys($roles));
                }
            ],
            // 'created_at',
            // 'updated_at',

            [
                'class' => '\kartik\grid\ActionColumn',
                'header' => Yii::t('common', 'Actions'),
                'vAlign' => GridView::ALIGN_MIDDLE,
                'template' => '{role} {update} {delete}',
                'buttons' => [
                    'role' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('admin', 'role'),
                            'aria-label' => Yii::t('admin', 'role'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-info'
                        ];
                        return Html::a('<i class="fa fa-fw fa-key"></i> ' . Yii::t('admin', 'role'), ['role', 'id' => $model->id], $options);
                    },
                    'update' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'update'),
                            'aria-label' => Yii::t('common', 'update'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-warning'
                        ];
                        return Html::a('<i class="fa fa-fw fa-pencil"></i> ' . Yii::t('common', 'update'), ['update', 'id' => $model->id], $options);
                    },
                    'delete' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'delete'),
                            'aria-label' => Yii::t('common', 'delete'),
                            'data-pjax' => '0',
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'class' => 'btn btn-xs btn-danger'
                        ];
                        return Html::a('<i class="fa fa-fw fa-trash"></i> ' . Yii::t('common', 'delete'), ['delete', 'id' => $model->id], $options);
                    }
                ],
            ]
        ],
        'panel' => [
            'heading' => false,
            'before' => '<div class="box-header pull-left">
                    <i class="fa fa-fw fa-sun-o"></i><h3 class="box-title">' . Yii::t('common', 'message_manage') . '</h3>
                </div>',
            'after' => '<div class="pull-left" style="margin-top: 8px">{summary}</div><div class="kv-panel-pager pull-right">{pager}</div><div class="clearfix"></div>',
            'footer' => false,
            //'footer' => '<div class="pull-left">'
            //    . Html::button('<i class="glyphicon glyphicon-remove-circle"></i>' . Yii::t('common', 'batch'), ['class' => 'btn btn-primary', 'id' => 'bulk_forbid'])
            //    . '</div>',
            //'footerOptions' => ['style' => 'padding:5px 15px']
        ],
        'panelFooterTemplate' => '{footer}<div class="clearfix"></div>',
        'toolbar' => [
            [
                'content' =>
                    Html::a('<i class="fa fa-plus"></i> ' . Yii::t('common', 'create'), ['create'], ['data-pjax' => 0, 'class' => 'btn btn-success', 'title' => Yii::t('common', 'create')]) . ' ' .
                    Html::a('<i class="fa fa-repeat"></i> ' . Yii::t('common', 'reset'), ['index'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('common', 'reset')])
            ],
            '{toggleData}',
            '{export}'
        ],

    ]); ?>
</div>
