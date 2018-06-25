<?php

use backend\modules\shop\models\Goods;
use backend\modules\shop\models\GoodsCategory;
use kartik\widgets\FileInput;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\Goods */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-form">

    <?php $form = ActiveForm::begin([
        'id' => 'goods-form',
        'options' => ['class' => 'box-body']
    ]); ?>

    <?php $goodsSn = $form->field($model, 'goods_sn', ['options' => ['class' => 'form-group c-md-6']])
        ->hint(Yii::t('goods', 'goods_sn_hint'))
        ->textInput(['maxlength' => true]) ?>

    <?php $goodsBarcode = $form->field($model, 'goods_barcode', ['options' => ['class' => 'form-group c-md-6']])->textInput(['maxlength' => true]) ?>

    <?php $title = $form->field($model, 'title', ['options' => ['class' => 'form-group c-md-6']])->textInput(['maxlength' => true]) ?>

    <?php $subTitle = $form->field($model, 'sub_title', ['options' => ['class' => 'form-group c-md-6']])->textarea(['rows' => 5]) ?>

    <?php $categoryId = $form->field($model, 'category_id', ['options' => ['class' => 'form-group c-md-6']])
        ->hint(Yii::t('goods', 'Please create goods category first!'))
        ->widget(Select2::classname(), [
            'data' => GoodsCategory::getGoodsCategoryTreeOptions(false),
            'options' => [
                'prompt' => Yii::t('common', 'Please Select...'),
                'encode' => false,
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]) ?>

    <?php $brandId = $form->field($model, 'brand_id', ['options' => ['class' => 'form-group c-md-6']])->widget(Select2::classname(), [
        'data' => [],//todo,品牌选择
        'options' => [
            'prompt' => Yii::t('common', 'Please Select...'),
            'encode' => false,
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>

    <?php $price = $form->field($model, 'price', ['options' => ['class' => 'form-group c-md-6']])
        ->hint(Yii::t('goods', 'Accurate to the second decimal place'))->textInput() ?>

    <?php $unit = $form->field($model, 'unit', ['options' => ['class' => 'form-group c-md-6']])
        ->hint(Yii::t('goods', 'unit_hint'))->textInput(['maxlength' => true]) ?>

    <?php $marketPrice = $form->field($model, 'market_price', ['options' => ['class' => 'form-group c-md-6']])
        ->hint(Yii::t('goods', 'Accurate to the second decimal place'))->textInput() ?>

    <?php $costPrice = $form->field($model, 'cost_price', ['options' => ['class' => 'form-group c-md-6']])
        ->hint(Yii::t('goods', 'Accurate to the second decimal place'))->textInput() ?>

    <?php $img = $form->field($model, 'img', ['options' => ['class' => 'form-group c-md-6']])->textInput(['maxlength' => true]) ?>

    <?php
    //字段名
    $imgOthersName = Html::getInputName($model, 'img_others');
    //设置初始参数
    $imgOthersValueArr = explode(',', $model->img_others);
    $initialPreviewConfig = [];
    foreach ($imgOthersValueArr as $item) {
        $initialPreviewConfig[] = [
            'caption' => basename($item),
            'url' => Url::to(['upload', 'action' => 'delete']),//ajax删除路径
            'key' => $item
        ];
    }
    $imgOthers = $form->field($model, 'img_others', ['options' => ['class' => 'form-group c-md-8']])
        ->hint(Yii::t('goods', 'img_others_hint'))
        ->widget(FileInput::classname(), [
            'options' => [
                'accept' => 'image/*',
                'multiple' => true,
                'hiddenOptions' => ['value' => $model->img_others, 'id' => 'img_others']//隐藏字段value，增加隐藏字段ID为字段名，方便rule验证
            ],
            'pluginOptions' => [
                'maxFileCount' => 6,
                'validateInitialCount' => true,//已上传也计入最大个数
                'uploadUrl' => Url::to(['upload', 'action' => 'upload']),//ajax上传路径
                'uploadExtraData' => [
                    'name' => $imgOthersName,//表字段名称,也可以在独立action中指定
                ],
                'showPreview' => true,
                'showClose' => false,
                'showUpload' => false,//异步上传时，批量上传很大概率会出现第一个被第二个覆盖的bug，所以这里设置只能单个点击上传
                'initialPreview' => empty($model->img_others) ? [] : $imgOthersValueArr,
                'initialPreviewConfig' => $initialPreviewConfig,
                'initialPreviewAsData' => true,
                'overwriteInitial' => false,//多文件不覆盖原有的，单文件覆盖
            ],
            'pluginEvents' => [
                //单个点击上传完毕后给隐藏表单赋值
                'fileuploaded' => new JsExpression("function (event,data,previewId,index){
                        var hiddenEle=$('input[type=\'hidden\'][name=\'" . $imgOthersName . "\']');
                        var hiddenValue=hiddenEle.val();
                        var key=data.response.key;
                        if(hiddenValue){
                            hiddenValue=hiddenValue+','+key;
                        }else{
                            hiddenValue=key;
                        }
                        hiddenEle.val(hiddenValue);
                       }"),
                //移动排序交换隐藏表单值的位置
                'filesorted' => new JsExpression("function (event,params){
                        var hiddenEle=$('input[type=\'hidden\'][name=\'" . $imgOthersName . "\']');
                        var hiddenValue=hiddenEle.val();
                        var hiddenValueArr=hiddenValue.split(',');
                        var oldIndex=params.oldIndex;
                        var newIndex=params.newIndex;
                        var tmp=hiddenValueArr[oldIndex];
                        hiddenValueArr[oldIndex]=hiddenValueArr[newIndex];
                        hiddenValueArr[newIndex]=tmp;
                        hiddenEle.val(hiddenValueArr.join(','));
                       }"),
                //单个点击删除时移除本隐藏表单内的值
                'filedeleted' => new JsExpression("function (event,key,jqXHR,data){
                        var hiddenEle=$('input[type=\'hidden\'][name=\'" . $imgOthersName . "\']');
                        var hiddenValue=hiddenEle.val();
                        var hiddenValueArr=hiddenValue.split(',');
                        var index=$.inArray(key,hiddenValueArr);
                        if(index!==-1){
                            hiddenValueArr.splice(index,1);
                            hiddenEle.val(hiddenValueArr.join(','));
                        }
                       }"),
            ]
        ])
    ?>

    <?php $content = $form->field($model, 'content', ['options' => ['class' => 'form-group c-md-6']])
        ->widget('kucha\ueditor\UEditor', [
            'clientOptions' => [
                //上传地址，需修改为上方action一致，默认是upload，但和文件上传同一名字，所以修改为此
                'serverUrl' => Url::to(['ueditorUpload']),
                //编辑区域大小
                'initialFrameHeight' => '300',
                //定制菜单
                'toolbars' => [
                    [
                        'fullscreen', 'source', 'undo', 'redo', '|',
                        'fontsize',
                        'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat',
                        'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|',
                        'forecolor', 'backcolor', '|',
                        'lineheight', '|',
                        'indent', '|'
                    ],
                    ['preview', 'simpleupload', 'insertimage', 'link', 'emotion', 'map', 'insertvideo', 'insertcode',]
                ]
            ]
        ]);
    ?>

    <?php $sales = $form->field($model, 'sales', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

    <?php $stock = $form->field($model, 'stock', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

    <?php $stockAlarm = $form->field($model, 'stock_alarm', ['options' => ['class' => 'form-group c-md-6']])
        ->hint(Yii::t('goods', 'stock_alarm_hint'))
        ->textInput() ?>

    <?php $stockType = $form->field($model, 'stock_type', ['options' => ['class' => 'form-group c-md-5']])
        ->widget(SwitchInput::classname(), [
            'type' => SwitchInput::RADIO,
            'items' => Goods::getStockTypeOptions(false, true),
            'labelOptions' => ['style' => 'font-weight:normal'],
            'pluginOptions' => ['size' => 'mini']
        ]) ?>

    <?php $weight = $form->field($model, 'weight', ['options' => ['class' => 'form-group c-md-6']])->textInput(['maxlength' => true]) ?>

    <?php $goodsProperty = Html::beginTag('div', ['class' => 'form-group c-md-6']) .
        Html::label(Yii::t('goods', 'Goods Property'), ['class' => 'control-label',]) . '<br>' .
        Html::activeCheckbox($model, 'is_new', ['labelOptions' => ['style' => 'font-weight:normal']]) .
        Html::activeCheckbox($model, 'is_hot', ['labelOptions' => ['style' => 'margin-left:10px;font-weight:normal']]) .
        Html::activeCheckbox($model, 'is_recommend', ['labelOptions' => ['style' => 'margin-left:10px;font-weight:normal']]) .
        Html::activeCheckbox($model, 'is_freight_free', ['labelOptions' => ['style' => 'margin-left:10px;font-weight:normal']]) .
        Html::tag('div', Yii::t('goods', 'is_freight_free_hint'), ['class' => 'hint-block']) .
        Html::endTag('div');
    ?>

    <?php $freightSetting = Html::beginTag('div', ['class' => 'form-group c-md-6']) .
        Html::label(Yii::t('goods', 'Freight Setting'), ['class' => 'control-label',]) . '<br>' .
        Html::activeRadioList($model, 'freight_type', Goods::getFreightTypeOptions(), [
            'item' => function ($index, $label, $name, $checked, $value) use ($model,$form) {
                if ($index == 0) {
                    $other = Html::beginTag('div', ['style' => 'float:left;width:50%;']) .
                        $form->field($model, 'freight_id', ['template'=>"{input}\n{hint}\n{error}"])
                            ->widget(Select2::classname(), [
                                'data' => [],//todo,获取运费模板列表
                                'options' => [
                                    'prompt' => Yii::t('common', 'Please Select...'),
                                    'encode' => false
                                ],
                            ]) .
                        Html::endTag('div') .
                        Html::tag('div', '', ['style' => 'clear:both']);
                } else {
                        $other = $form->field($model, 'freight_price', ['options' => ['style'=>'width:50%;float:left;'],'template'=>"{input}\n{hint}\n{error}"])
                                ->textInput(['maxlength' => true]).
                            Html::tag('div', '', ['style' => 'clear:both']);
                }
                return Html::radio($name, $checked, [
                        'value' => $value,
                        'label' => Html::encode($label),
                        'labelOptions' => ['style' => 'font-weight:normal;float:left;width:10%;line-height:34px;min-width:100px']
                    ]) . $other;
            }
        ]) .
        Html::tag('div', Yii::t('goods', 'freight_hint'), ['class' => 'hint-block']) .
        Html::endTag('div');
    ?>

    <?php $isLimit = $form->field($model, 'is_limit', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

    <?php $maxBuy = $form->field($model, 'max_buy', ['options' => ['class' => 'form-group c-md-6']])
        ->hint(Yii::t('goods', 'max_buy_hint'))
        ->textInput() ?>

    <?php $minBuy = $form->field($model, 'min_buy', ['options' => ['class' => 'form-group c-md-6']])
        ->hint(Yii::t('goods', 'min_buy_hint'))
        ->textInput() ?>

    <?php $userMaxBuy = $form->field($model, 'user_max_buy', ['options' => ['class' => 'form-group c-md-6']])
        ->hint(Yii::t('goods', 'user_max_buy_hint'))
        ->textInput() ?>

    <?php $giveIntegral = $form->field($model, 'give_integral', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

    <?php $sort = $form->field($model, 'sort', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

    <?php $status = $form->field($model, 'status', ['options' => ['class' => 'form-group c-md-5']])
        ->widget(SwitchInput::classname(), [
            'type' => SwitchInput::RADIO,
            'items' => Goods::getStatusOptions(false, true),
            'labelOptions' => ['style' => 'font-weight:normal'],
            'pluginOptions' => ['size' => 'mini']
        ]) ?>

    <?= Tabs::widget([
        'items' => [
            [
                'label' => Yii::t('goods', 'basic'),
                'content' =>
                    $categoryId .
                    $title .
                    $subTitle .
                    $price .
                    $marketPrice .
                    $costPrice .
                    $unit .
                    $goodsProperty .
                    $freightSetting .
                    $giveIntegral .
                    $sort .
                    $status
            ],
            [
                'label' => Yii::t('goods', 'stock'),
                'content' => $goodsSn .
                    $goodsBarcode .
                    $weight .
                    $stock .
                    $stockAlarm .
                    $stockType
            ],
            [
                'label' => Yii::t('goods', 'image'),
                'content' => $imgOthers
            ],
            [
                'label' => Yii::t('goods', 'content'),
                'content' => $brandId .
                    $content
            ],
            [
                'label' => Yii::t('goods', 'limit'),
                'content' => $maxBuy .
                    $minBuy .
                    $userMaxBuy
            ],
        ],
        'itemOptions' => ['class' => 'p-10']
    ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'create') : Yii::t('common', 'update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-warning']) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$js = <<<eof
    //红星提示
    $('.required').each(function(){
        var label=$(this).children(':first');
        label.html(label.html()+'<i style="color:red">*</i>');
    });
    //前端js验证出错后切换标签页
    $("#goods-form").on('afterValidate',function(event, messages, errorAttributes){
        $.each(errorAttributes,function(i,v){
            var errorContainer=v.container,
                tab;
            //特殊处理
            if(errorContainer=='.field-goods-freight_id' || errorContainer=='.field-goods-freight_price'){
                tab=$(errorContainer).parent().parent().parent().parent();
            }else{
                tab=$(errorContainer).parent();
            }
            var tabId=tab.attr('id');
            $('a[href="#'+tabId+'"]').tab('show');
            return false;
        })
    })
    //后端服务器验证出错后切换标签页
    var errorContainer=$(".has-error:first"),
        tab;
    if(errorContainer.hasClass('field-goods-freight_id') || errorContainer.hasClass('field-goods-freight_price')){
        tab=$(errorContainer).parent().parent().parent().parent();
    }else{
        tab= errorContainer.parent();
    }
    var tabId=tab.attr('id');
    $('a[href="#'+tabId+'"]').tab('show');
eof;
$this->registerJs($js);
?>
