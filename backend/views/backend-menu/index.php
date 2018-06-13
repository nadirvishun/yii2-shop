<?php

use backend\models\BackendMenu;
use dmstr\widgets\Menu;
use kartik\editable\Editable;
use kartik\popover\PopoverX;
use yii\helpers\Html;
use dkhlystov\widgets\TreeGrid;

//use leandrogehlen\treegrid\TreeGrid;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\BackendMenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $initial backend\controllers\BackendMenuController */

$this->title = Yii::t('backend_menu', 'Backend Menus');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backend-menu-index grid-view box box-primary">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box-header with-border">
        <div class="box-header pull-left">
            <i class="fa fa-fw fa-sun-o"></i>
            <h3 class="box-title"><?= Yii::t('common', 'message_manage') ?></h3>
        </div>
        <div class="btn-group pull-right">
            <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('common', 'create'), ['create'], ['data-pjax' => 0, 'class' => 'btn btn-success', 'title' => Yii::t('common', 'create')]) . ' ' .
            Html::a('<i class="fa fa-repeat"></i> ' . Yii::t('common', 'reset'), ['index'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('common', 'reset')]) ?>
        </div>
    </div>
    <?=
    TreeGrid::widget([
        'rowOptions' => ['class' => 'expanded'],//默认展开
        'tableOptions' => ['class' => 'table table-bordered  table-hover table-striped'],
        'dataProvider' => $dataProvider,
        'parentIdAttribute' => 'pid',
        'showRoots' => true,
        'lazyLoad' => false,
        'emptyTextOptions' => ['class' => 'empty p-10'],
        'pluginOptions'=>[

        ],
//        'initialNode' => $initial,
//        'moveAction' => ['move'],
        'columns' => [
            'name',
            'id',
//            'pid',
            [
                'attribute' => 'url',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a($model->url,BackendMenu::mergeUrl($model->url,$model->url_param),['target'=>'_blank']);
                },
                'format'=>'raw'
            ],
            'url_param',
            [
                'attribute' => 'icon',
                'value' => function ($model, $key, $index, $column) {
                    return "<i class='".Menu::$iconClassPrefix.$model->icon."'></i>&nbsp;&nbsp;".$model->icon;
                },
                'format'=>'raw'
            ],
            [
                'attribute' => 'sort',
                'format'=>'raw',
                'value' => function ($model, $key, $index, $column) {
                    return Editable::widget([
                        'name'=>'sort',
                        'value' => $model->sort,
                        'header' => $model->getAttributeLabel('sort'),
                        'size'=>'md',
                        'placement'=>PopoverX::ALIGN_LEFT,//左侧弹出
                        'beforeInput' => Html::hiddenInput('editableKey',$model->id).Html::hiddenInput('editableAttribute','sort')//传递ID和字段
                    ]);
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    return Editable::widget([
                        'name' => 'status',
                        'value' => $model->status,//原始值
                        'displayValueConfig' => BackendMenu::getStatusOptions(),//要显示的文字
                        'header' => $model->getAttributeLabel('status'),
                        'size' => 'md',
                        'placement' => PopoverX::ALIGN_LEFT,//左侧弹出
                        'inputType' => Editable::INPUT_SWITCH,
                        'options' => [
                            'options' => ['uncheck' => 0, 'value' => 1],//switch插件的参数
                            'pluginOptions' => ['size' => 'small'],
                        ],
                        'beforeInput' => Html::hiddenInput('editableKey', $model->id) . Html::hiddenInput('editableAttribute', 'status')//传递ID和字段
                    ]);
                }
            ],
            // 'created_by',
            // 'created_at',
            // 'updated_by',
            // 'updated_at',

            [
                'class' => '\yii\grid\ActionColumn',
                'header' => Yii::t('common', 'Actions'),
                'headerOptions'=>['style' => 'width:200px'],
                'template' => '{create} {update} {delete}',
                'buttons' => [
                    'create' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'create_sub'),
                            'aria-label' => Yii::t('common', 'create_sub'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-success'
                        ];
                        return Html::a('<i class="fa fa-fw fa-plus"></i>'.Yii::t('common', 'create_sub'), ['create', 'pid' => $model->id], $options);
                    },
                    'update' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'update'),
                            'aria-label' => Yii::t('common', 'update'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-warning'
                        ];
                        return Html::a('<i class="fa fa-fw fa-pencil"></i>'.Yii::t('common', 'update'), ['update', 'id' => $model->id], $options);
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
                        return Html::a('<i class="fa fa-fw fa-trash"></i>'.Yii::t('common', 'delete'), ['delete', 'id' => $model->id], $options);
                    }
                ]
            ],
        ]
    ]); ?>
</div>
<?php
    //设置js控制默认的三级菜单为折叠的
    $js=<<<EOF
$("tr[class*='treegrid-parent-']").treegrid('collapse');
EOF;
    $this->registerJs($js);
?>