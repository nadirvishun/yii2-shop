<?php

use backend\modules\shop\models\Goods;
use kartik\editable\Editable;
use kartik\popover\PopoverX;
use kartik\switchinput\SwitchInput;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\shop\models\search\Goods */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('goods', 'Goods');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-index grid-view box box-primary">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id'=>'goods_grid',
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
//                'width' => '36px',
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
//                'width' => '90px',
                'hAlign' => 'center',
                'editableOptions' => function ($model, $key, $index) {
                    return [
                        'value' => Yii::$app->formatter->asDecimal($model->price / 100, 2),
                        'header' => $model->getAttributeLabel('price'),
                        'size' => 'md',
                        'options'=>[
                                'value'=>Yii::$app->formatter->asDecimal($model->price / 100, 2),
                        ]
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
//                'width' => '90px',
                'format' => 'raw',
                'hAlign' => 'center',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => Goods::getStockAlarmOptions(),
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
                        'header' => $model->getAttributeLabel('stock'),
                        'size' => 'md',
                    ];
                },
                'value' => function ($model, $key, $index, $column) {
                    if ($model->stock==0 ||($model->stock_alarm !== 0 && $model->stock_alarm >= $model->stock)) {
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
//                'width' => '36px',
                'hAlign' => 'center',
                'editableOptions' => function ($model, $key, $index) {
                    return [
                        'header' => $model->getAttributeLabel('sort'),
                        'size' => 'md',
                        'placement' => PopoverX::ALIGN_LEFT,//左侧弹出
                    ];
                },
            ],
            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'status',
                'vAlign' => 'middle',
//                'width' => '90px',
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
            'footer' =>'<div class="pull-left" style="width:100px">'.
                Select2::widget([
                    'id' => 'batch_type',
                    'name' => 'batch_type',
                    'data' => Goods::getBatchOperations(),
                ])
                . '</div>'
                .'<div class="pull-left">'
                . Html::button('<i class="fa fa-flash"></i> ' . Yii::t('goods', 'batch operation'), ['class' => 'btn btn-primary', 'id' => 'batch_operation'])
                . '</div>',
            'footerOptions' => ['style' => 'padding:5px 15px']
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
<?php

$js = <<<eof
$('#batch_operation').on('click',function(){
    //判定是否选中
    var ids=$('#goods_grid').yiiGridView('getSelectedRows');
    var keys=ids.length;
    if(keys<=0){
        krajeeDialog.alert('清选择要操作的列');
    }else{
        //弹出确认是否执行
        krajeeDialog.confirm('是否确认此操作',function(result){
            if(result){
                //要改变的动作
                var batchType=$('#batch_type').val();
                $.ajax({
                   
                    
                })
            }
        });
    }
})
eof;
$this->registerJs($js);

?>