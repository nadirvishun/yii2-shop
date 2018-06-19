<?php

use dkhlystov\widgets\NestedTreeGrid;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('goods_category', 'Goods Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-category-index grid-view box box-primary">


    <div class="box-header with-border">
        <div class="box-header pull-left">
            <i class="fa fa-fw fa-sun-o"></i>
            <h3 class="box-title"><?= Yii::t('common', 'message_manage') ?></h3>
        </div>
        <div class="btn-group pull-right">
            <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('common', 'create'), ['create'], ['data-pjax' => 0, 'class' => 'btn btn-success', 'title' => Yii::t('common', 'create')]) . ' ' .
            Html::a('<i class="fa fa-repeat"></i> ' . Yii::t('common', 'reset'), ['index'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('common', 'reset')])?>
        </div>
    </div>
    <?= NestedTreeGrid::widget([
        'tableOptions' => ['class' => 'table table-bordered  table-hover table-striped'],
        'rowOptions'=>['class'=>'expanded'],
        'emptyTextOptions'=>['class'=>'empty p-10'],
        'dataProvider' => $dataProvider,
        'showRoots' => true,
        'lazyLoad' => false,
        'moveAction' => ['move'],
        'pluginOptions'=>[
                //修改顶级分类也能移动
                'onMoveOver' => new  \yii\web\JsExpression('function(item, helper, target, position) {
                    if (item.treegrid("getDepth") == 1) return false;
                    if ((position == 0 || position == 2) && target.treegrid("getDepth") == 1) return false;
                    return true;
                }')
        ],
        'columns' => [
            'name',
            'id',
//            'tree',
            'img',
//            'lft',
            // 'rgt',
            // 'depth',
            // 'adv_img',
            // 'adv_type',
            // 'adv_value',
             'is_recommend',
             'sort',
             'status',
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
</div>
