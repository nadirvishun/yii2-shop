<?php

use backend\modules\shop\models\GoodsCategory;
use dkhlystov\widgets\NestedTreeGrid;
use kartik\dialog\Dialog;
use kartik\editable\Editable;
use kartik\popover\PopoverX;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('goods_category', 'Goods Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<!--自定义弹出框-->
<?= Dialog::widget([
    'libName' => 'krajeeDialogCus',
    'options' => [
        'draggable' => true,
        'overrideYiiConfirm' => false,
//        'type'=>Dialog::TYPE_SUCCESS,
        'size' => Dialog::SIZE_SMALL,
    ]
]); ?>
<div class="goods-category-index grid-view box box-primary">

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
    <?= NestedTreeGrid::widget([
        'tableOptions' => ['class' => 'table table-bordered  table-hover table-striped'],
        'rowOptions' => ['class' => 'expanded'],
        'emptyTextOptions' => ['class' => 'empty p-10'],
        'dataProvider' => $dataProvider,
        'showRoots' => false,//统一在一个数内，所以不显示顶级的
        'lazyLoad' => false,
        'moveAction' => ['move'],
        'pluginOptions' => [
            //修改为post提交，且增加提示
            'onMove' => new \yii\web\JsExpression('function(item, target, position) {
                    var $el = this;
                    $el.treegrid("option", "enableMove", false);
                    $.post("' . Url::to(["move"]) . '", {
                        id: item.treegrid("getId"),
                        target: target.treegrid("getId"),
                        position: position
                    },function(data) {
                        $(".loader-overlay").hide();
                        if(data.code==0){
                            $el.treegrid("option", "enableMove", true);
                        }
                        krajeeDialogCus.alert(data.msg);
                    },"json").fail(function(xhr) {
                        $(".loader-overlay").hide();
                        krajeeDialogCus.alert(xhr.responseText);
                    });
                }'),
            //增加加载提示，todo,这里需要一个额外的参数：https://github.com/dkhlystov/yii2-treegrid/issues/12
            'onMoveStop' => new \yii\web\JsExpression('function(item) {
                    $(".loader-overlay").show();
                }'),
        ],
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    if (!empty($model->img)) {
                        return Html::a(
                                Html::img($model->img, ['style' => 'width:28px;border:1px solid #ccc']),
                                $model->img,
                                ['target' => '_blank']
                            ) . '&nbsp;' . $model->name;
                    } else {
                        return $model->name;
                    }
                }
            ],
            'id',
//            'tree',
//            'img',
//            'lft',
            // 'rgt',
            // 'depth',
            // 'adv_img',
//            'adv_type',
//             'adv_value',
            [
                'attribute' => 'is_recommend',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    return Editable::widget([
                        'name' => 'is_recommend',
                        'id' => 'is_recommend-' . $model->id,
                        'value' => $model->is_recommend,//原始值
                        'displayValueConfig' => GoodsCategory::getRecommendOptions(),//要显示的文字
                        'header' => $model->getAttributeLabel('is_recommend'),
                        'size' => 'md',
                        'placement' => PopoverX::ALIGN_LEFT,//左侧弹出
                        'inputType' => Editable::INPUT_SWITCH,
                        'options' => [
                            'options' => ['uncheck' => 0, 'value' => 1],//switch插件的参数
                            'pluginOptions' => ['size' => 'small'],
                        ],
                        'beforeInput' => Html::hiddenInput('editableKey', $model->id) . Html::hiddenInput('editableAttribute', 'is_recommend')//传递ID和字段
                    ]);
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    return Editable::widget([
                        'name' => 'status',
                        'id' => 'status-' . $model->id,
                        'value' => $model->status,//原始值
                        'displayValueConfig' => GoodsCategory::getStatusOptions(),//要显示的文字
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
                'headerOptions' => ['style' => 'width:200px'],
                'template' => '{create} {update} {delete}',
                'buttons' => [
                    'create' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'create_sub'),
                            'aria-label' => Yii::t('common', 'create_sub'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-success'
                        ];
                        return Html::a('<i class="fa fa-fw fa-plus"></i> ' . Yii::t('common', 'create_sub'), ['create', 'pid' => $model->id], $options);
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
                ]
            ],
        ]
    ]); ?>
    <!-- 加载层   -->
    <div class="loader-overlay">
        <div class="loader"></div>
    </div>
</div>
