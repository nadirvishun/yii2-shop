<?php

use backend\modules\shop\models\Goods;
use kartik\editable\Editable;
use kartik\popover\PopoverX;
use kartik\switchinput\SwitchInput;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\shop\models\search\Goods */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('goods', 'Goods');
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="goods-index grid-view box box-primary">

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <?= GridView::widget([
            'id' => 'goods_grid',
            'dataProvider' => $dataProvider,
            'hover' => true,
            'pjax' => true,
            'condensed' => true,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'class' => '\kartik\grid\CheckboxColumn',
                    'rowSelectedClass' => GridView::TYPE_INFO
                ],
                [
                    'attribute' => 'id',
                    'vAlign' => 'middle',
                    'width' => '36px',
                    'headerOptions' => ['class' => 'kartik-sheet-style']
                ],
                [
                    'attribute' => 'title',
                    'format' => 'raw',
                    'vAlign' => 'middle',
                    'value' => function ($model, $key, $index, $column) {
                        return Html::img($model->img, ['width' => '50px', 'style' => 'float:left']) .
                            Html::beginTag('div', ['style' => 'float:left;height:50px;margin-left:5px']) .
                            Html::tag('div', $model->title, ['style' => 'line-height:25px']) .
                            Html::tag('div', '<span style="color:#999">货号：</span>' . $model->goods_sn, ['style' => 'line-height:25px']) .
                            Html::endTag('div');
                    }
                ],
//            'goods_sn',
//            'goods_barcode',
//            'sub_title',
                // 'category_id',
                // 'brand_id',
                [
                    'class' => '\kartik\grid\EditableColumn',
                    'attribute' => 'price',
                    'vAlign' => 'middle',
                    'width' => '90px',
                    'hAlign' => 'center',
                    'readonly' => function($model, $key, $index, $widget) {
                        //多规格时，价格和库存不能修改
                        return $model->has_spec ? true : false;
                    },
                    'editableOptions' => function ($model, $key, $index) {
                        return [
                            'value' => Yii::$app->formatter->asDecimal($model->price / 100, 2),
                            'header' => $model->getAttributeLabel('price'),
                            'size' => 'md',
                            'options' => [
                                'value' => Yii::$app->formatter->asDecimal($model->price / 100, 2),
                            ],
                            //统一在index方法中修改
                            'ajaxSettings' => ['url' => Url::to('/shop/goods/index')]
                        ];
                    },
                    'value' => function ($model, $key, $index, $column) {
                        return Yii::$app->formatter->asDecimal($model->price / 100, 2);
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
                    'class' => '\kartik\grid\EditableColumn',
                    'attribute' => 'stock',
                    'vAlign' => 'middle',
                    'width' => '90px',
                    'format' => 'raw',
                    'hAlign' => 'center',
                    'readonly' => function($model, $key, $index, $widget) {
                        return $model->has_spec ? true : false;
                    },
                    /*'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => Goods::getStockAlarmOptions(),
                        'options' => [
                            'prompt' => Yii::t('common', 'Please Select...'),
                        ],
                        'hideSearch' => true,
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ],*/
                    'editableOptions' => function ($model, $key, $index) {
                        return [
                            'header' => $model->getAttributeLabel('stock'),
                            'size' => 'md',
                            'ajaxSettings' => ['url' => Url::to('/shop/goods/index')]
                        ];
                    },
                    'value' => function ($model, $key, $index, $column) {
                        if ($model->checkStockAlarm($model->stock, $model->stock_alarm, $model->has_spec, $model->id)) {
                            return '<span style="color:red">' . $model->stock . '</span>';
                        } else {
                            return $model->stock;
                        }
                    }
                ],
                // 'stock_alarm',
                // 'stock_type',
                // 'weight',
//             'is_freight_free',
                // 'freight_type',
                // 'freight_id',
                // 'freight_price',
                [
                    'class' => '\kartik\grid\EditableColumn',
                    'attribute' => 'is_new',
                    'vAlign' => 'middle',
                    'hAlign' => 'center',
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => Goods::getGoodsPropertyOptions('is_new'),
                        'options' => [
                            'prompt' => Yii::t('common', 'Please Select...'),
                        ],
                        'hideSearch' => true,
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ],
                    'editableOptions' => function ($model, $key, $index) {
                        return [
                            'value' => $model->is_new,//原始值
                            'displayValueConfig' => Goods::getGoodsPropertyOptions('is_new'),//要显示的文字
                            'header' => $model->getAttributeLabel('is_new'),
                            'size' => 'md',
                            'placement' => PopoverX::ALIGN_LEFT,//左侧弹出
                            'inputType' => Editable::INPUT_SWITCH,
                            'options' => [
                                'options' => ['uncheck' => 0, 'value' => 1],//switch插件的参数
                                'pluginOptions' => ['size' => 'small'],
                            ],
                            'ajaxSettings' => ['url' => Url::to('/shop/goods/index')]
                        ];
                    },
                    'value' => function ($model, $key, $index, $column) {
                        return Goods::getGoodsPropertyOptions('is_new', $model->is_new);
                    }
                ],
                [
                    'class' => '\kartik\grid\EditableColumn',
                    'attribute' => 'is_hot',
                    'vAlign' => 'middle',
                    'hAlign' => 'center',
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => Goods::getGoodsPropertyOptions('is_hot'),
                        'options' => [
                            'prompt' => Yii::t('common', 'Please Select...'),
                        ],
                        'hideSearch' => true,
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ],
                    'editableOptions' => function ($model, $key, $index) {
                        return [
                            'value' => $model->is_hot,//原始值
                            'displayValueConfig' => Goods::getGoodsPropertyOptions('is_hot'),//要显示的文字
                            'header' => $model->getAttributeLabel('is_hot'),
                            'size' => 'md',
                            'placement' => PopoverX::ALIGN_LEFT,//左侧弹出
                            'inputType' => Editable::INPUT_SWITCH,
                            'options' => [
                                'options' => ['uncheck' => 0, 'value' => 1],//switch插件的参数
                                'pluginOptions' => ['size' => 'small'],
                            ],
                            'ajaxSettings' => ['url' => Url::to('/shop/goods/index')]
                        ];
                    },
                    'value' => function ($model, $key, $index, $column) {
                        return Goods::getGoodsPropertyOptions('is_hot', $model->is_new);
                    }
                ],
                [
                    'class' => '\kartik\grid\EditableColumn',
                    'attribute' => 'is_recommend',
                    'vAlign' => 'middle',
                    'hAlign' => 'center',
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => Goods::getGoodsPropertyOptions('is_recommend'),
                        'options' => [
                            'prompt' => Yii::t('common', 'Please Select...'),
                        ],
                        'hideSearch' => true,
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ],
                    'editableOptions' => function ($model, $key, $index) {
                        return [
                            'value' => $model->is_recommend,//原始值
                            'displayValueConfig' => Goods::getGoodsPropertyOptions('is_recommend'),//要显示的文字
                            'header' => $model->getAttributeLabel('is_recommend'),
                            'size' => 'md',
                            'placement' => PopoverX::ALIGN_LEFT,//左侧弹出
                            'inputType' => Editable::INPUT_SWITCH,
                            'options' => [
                                'options' => ['uncheck' => 0, 'value' => 1],//switch插件的参数
                                'pluginOptions' => ['size' => 'small'],
                            ],
                            'ajaxSettings' => ['url' => Url::to('/shop/goods/index')]
                        ];
                    },
                    'value' => function ($model, $key, $index, $column) {
                        return Goods::getGoodsPropertyOptions('is_recommend', $model->is_new);
                    }
                ],
                // 'is_limit',
                // 'max_buy',
                // 'min_buy',
                // 'user_max_buy',
                // 'give_integral',
                [
                    'class' => '\kartik\grid\EditableColumn',
                    'attribute' => 'sort',
                    'vAlign' => 'middle',
                    'width' => '36px',
                    'hAlign' => 'center',
                    'editableOptions' => function ($model, $key, $index) {
                        return [
                            'header' => $model->getAttributeLabel('sort'),
                            'size' => 'md',
                            'placement' => PopoverX::ALIGN_LEFT,//左侧弹出
                            'ajaxSettings' => ['url' => Url::to('/shop/goods/index')]
                        ];
                    },
                ],
                [
                    'class' => '\kartik\grid\EditableColumn',
                    'attribute' => 'status',
                    'vAlign' => 'middle',
                    'width' => '90px',
                    'hAlign' => 'center',
                    'mergeHeader' => true,
                    'editableOptions' => function ($model, $key, $index) {
                        return [
                            'value' => $model->status,//原始值
                            'displayValueConfig' => Goods::getStatusOptions(),//要显示的文字
                            'header' => $model->getAttributeLabel('status'),
                            'size' => 'lg',
                            'placement' => PopoverX::ALIGN_LEFT,//左侧弹出
                            'inputType' => Editable::INPUT_SWITCH,
                            'options' => [
                                'type' => SwitchInput::RADIO,
                                'items' => Goods::getStatusOptions(false, true),
                                'labelOptions' => ['style' => 'font-weight:normal'],
                                'pluginOptions' => ['size' => 'mini']
                            ],
                            'ajaxSettings' => ['url' => Url::to('/shop/goods/index')]
                        ];
                    },
                    'value' => function ($model, $key, $index, $column) {
                        return Goods::getStatusOptions($model->status);
                    }
                ],
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
//            'footer' => false,
                'footer' => '<div class="pull-left" style="width:100px">' .
                    Select2::widget([
                        'id' => 'batch_type',
                        'name' => 'batch_type',
                        'data' => Goods::getBatchOperations('name'),
                    ])
                    . '</div>'
                    . '<div class="pull-left">'
                    . Html::button('<i class="fa fa-flash"></i> ' . Yii::t('goods', 'batch operation'), ['class' => 'btn btn-primary', 'id' => 'batch_operation'])
                    . '</div>',
                'footerOptions' => ['style' => 'padding:5px 15px']
            ],
            'panelFooterTemplate' => '{footer}<div class="clearfix"></div>',
            'toolbar' => [
                [
                    'content' =>
                        Html::a('<i class="fa fa-plus"></i> ' . Yii::t('common', 'create'), ['create'], ['data-pjax' => 0, 'class' => 'btn btn-success', 'title' => Yii::t('common', 'create')]) . ' ' .
                        Html::a('<i class="fa fa-repeat"></i> ' . Yii::t('common', 'reset'), Url::to(), ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('common', 'reset')])
                ],
                '{toggleData}',
                '{export}'
            ],

        ]); ?>
    </div>
<?php
//批量操作
$url = Url::to(['batch-operation']);
$mustSelect = Yii::t('common', 'Please select the column!');
$confirm = Yii::t('common', 'Are you sure you want to operate this item?');
$js = <<<eof
//由于pjax加载，所以通过代理来保持事件正常
$('#goods_grid-pjax').on('click','#batch_operation',function(){
    //判定是否选中
    var ids=$('#goods_grid').yiiGridView('getSelectedRows');
    var keys=ids.length;
    if(keys<=0){
        krajeeDialog.alert('$mustSelect');
    }else{
        //弹出确认是否执行
        krajeeDialog.confirm('$confirm',function(result){
            if(result){
                //要改变的动作
                var batchType=$('#batch_type').val();
                $.ajax({
                   url:'$url',
                   type:"POST",
                   data:{batch_type:batchType,ids:JSON.stringify(ids)},
                   dataType:'json',
                   success:function(data){
                        if(data.code==0){
                            krajeeDialog.alert(data.msg);
                            setTimeout(function () {
                                window.location.href=location.href;
                            },1000);
                        }else{
                            krajeeDialog.alert(data.msg);
                        }
                   }
                })
            }
        });
    }
})
//pjax加载后重新注册select2相关
$(document).on('pjax:complete', function() {
    var el=$('#batch_type');
    var id = el.attr('id');
    var settings = el.attr('data-krajee-select2');
    settings = window[settings];
    $.when(el.select2(settings)).done(initS2Loading(id));
})
eof;
$this->registerJs($js);

?>