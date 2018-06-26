<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\shop\models\search\Goods */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('goods', 'Goods');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-index grid-view box box-primary">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'hover' => true,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => '\kartik\grid\CheckboxColumn',
                'rowSelectedClass' => GridView::TYPE_INFO
            ],

            'id',
            'title',
            'goods_sn',
//            'goods_barcode',
//            'sub_title',
            // 'category_id',
            // 'brand_id',
            [
                'attribute' => 'price',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    return Yii::$app->formatter->asDecimal($model->price/100,2);
                }
            ],
            // 'unit',
            // 'market_price',
            // 'cost_price',
            // 'img',
            // 'img_others:ntext',
            // 'content:ntext',
            // 'sales',
            // 'real_sales',
            // 'click',
            // 'collect',
            [
                'attribute' => 'stock',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    if($model->stock_alarm!==0 && $model->stock_alarm >=$model->stock){
                        return '<i style="color:red">'.$model->stock.'</i>';
                    }else{
                        return $model->stock;
                    }
                }
            ],
            // 'stock_alarm',
            // 'stock_type',
            // 'weight',
            // 'is_freight_free',
            // 'freight_type',
            // 'freight_id',
            // 'freight_price',
            // 'is_new',
            // 'is_hot',
            // 'is_recommend',
            // 'is_limit',
            // 'max_buy',
            // 'min_buy',
            // 'user_max_buy',
            // 'give_integral',
             'sort',
             'status',
            // 'created_by',
            // 'created_at',
            // 'updated_by',
            // 'updated_at',

            [
                'class' => '\kartik\grid\ActionColumn',
                'header' => Yii::t('common', 'Actions'),
                'vAlign' => GridView::ALIGN_MIDDLE,
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('common', 'update'),
                            'aria-label' => Yii::t('common', 'update'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-xs btn-warning'
                        ];
                        return Html::a('<i class="fa fa-fw fa-pencil"></i> ' . Yii::t('common', 'update'), ['update', 'id' => $model->id], $options);
                    },
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
