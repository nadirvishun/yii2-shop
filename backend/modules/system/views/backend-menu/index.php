<?php

use backend\modules\system\models\BackendMenu;
use dmstr\widgets\Menu;
use kartik\editable\Editable;
use kartik\editable\EditablePjaxAsset;
use kartik\popover\PopoverX;
use yii\helpers\Html;
use dkhlystov\widgets\TreeGrid;

//use leandrogehlen\treegrid\TreeGrid;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\system\models\search\BackendMenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $initial backend\modules\system\controllers\BackendMenuController */

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
//        'rowOptions' => ['class' => 'expanded'],//默认展开
        'tableOptions' => ['class' => 'table table-bordered  table-hover table-striped'],
        'dataProvider' => $dataProvider,
        'parentIdAttribute' => 'pid',
        'showRoots' => true,
        'lazyLoad' => true,
        'emptyTextOptions' => ['class' => 'empty p-10'],
        'pluginOptions'=>[
            'source' => new \yii\web\JsExpression('function(id, response) {
                var $tr = this, token = Math.random().toString(36).substr(2);
                //已经存在的不再初始化
                var existTargetButton=[];
                $(".kv-editable-value").each(function(){
                    existTargetButton.push($(this).attr("id"));
                });
                $.get(window.location.href, {treegrid_id: id, treegrid_token: token}, function(data) {
                    response(data);
                    //ajax加载后重新初始化editable插件
                    $(".kv-editable-value").each(function(){
                        var targetButton=$(this).attr("id");
                        if($.inArray(targetButton,existTargetButton)===-1){
                            var arr=targetButton.split("-");
                            var attr=arr[0];
                            var id=attr+"-"+arr[1];
                            //初始化editable,目前editor配置参数没找到方法获取，先写死
                            //不设置参数的话，返回displayValueConfig相关参数获取不到，导致修改后0显示也是0，但应该显示文字“隐藏”
                            if(attr=="sort"){
                                $("#"+id+"-cont").editable(editable_eaca81be)
                            }else if(attr=="status"){
                                $("#"+id+"-cont").editable(editable_a690100c)
                                 //初始化switch，有点坑啊，没调用一个组件都要初始化，插件只做了pjax相关的处理
                                var opts = window[$("#"+id).attr("data-krajee-bootstrapSwitch")];
                                $("#"+id).bootstrapSwitch(opts);
                            }
                            //重新绑定提交等事件
                            $("#"+id+"-cont").editable("destroy").editable("create");
                            //初始化popover
                            initEditablePopover(targetButton);
                        }
                    });
                }, "json");
            }')
        ],
        'initialNode' => $initial,
//        'moveAction' => ['move'],
        'columns' => [
            'name',
            'id',
//            'pid',
            [
                'attribute' => 'url',
                'value' => function ($model, $key, $index, $column) {
                    //由于有modules，需要绝对路径
                    return Html::a($model->url,BackendMenu::mergeUrl('/' . $model->url,$model->url_param),['target'=>'_blank']);
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
                        'id'=>'sort-'.$model->id,
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
                        'id'=>'status-'.$model->id,
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
    //需要用到此js中从新加载editable的方法
    EditablePjaxAsset::register($this);
?>
