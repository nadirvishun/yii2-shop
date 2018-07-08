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

    <!-- 商品图片相关   -->
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
    $imgOthers = $form->field($model, 'img_others', ['options' => ['class' => 'form-group c-md-9']])
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
                        //上传后触发验证
                         $('#goods-form').yiiActiveForm('validateAttribute', 'goods-img_others')
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


    <!-- 商品详情相关   -->
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

    <?php $weight = $form->field($model, 'weight', ['options' => ['class' => 'form-group c-md-6']])
        ->hint(Yii::t('goods', 'unit:g'))->textInput(['maxlength' => true]) ?>


    <!-- 商品属性新品热销等相关   -->
    <?php $goodsProperty = Html::beginTag('div', ['class' => 'form-group c-md-6']) .
        Html::label(Yii::t('goods', 'Goods Property'), null, ['class' => 'control-label',]) . '<br>' .
        Html::activeCheckbox($model, 'is_new', ['labelOptions' => ['style' => 'font-weight:normal']]) .
        Html::activeCheckbox($model, 'is_hot', ['labelOptions' => ['style' => 'margin-left:10px;font-weight:normal']]) .
        Html::activeCheckbox($model, 'is_recommend', ['labelOptions' => ['style' => 'margin-left:10px;font-weight:normal']]) .
        Html::activeCheckbox($model, 'is_freight_free', ['labelOptions' => ['style' => 'margin-left:10px;font-weight:normal']]) .
        Html::tag('div', Yii::t('goods', 'is_freight_free_hint'), ['class' => 'hint-block']) .
        Html::endTag('div');
    ?>

    <!-- 商品运费相关相关   -->
    <?php $freightSetting = Html::beginTag('div', ['class' => 'form-group c-md-6']) .
        Html::label(Yii::t('goods', 'Freight Setting'), null, ['class' => 'control-label',]) . '<br>' .
        Html::activeRadioList($model, 'freight_type', Goods::getFreightTypeOptions(), [
            'item' => function ($index, $label, $name, $checked, $value) use ($model, $form) {
                if ($index == 0) {
                    $other = Html::beginTag('div', ['style' => 'float:left;width:50%;']) .
                        $form->field($model, 'freight_id', ['template' => "{input}\n{hint}\n{error}"])
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
                    $other = $form->field($model, 'freight_price', ['options' => ['style' => 'width:50%;float:left;'], 'template' => "{input}\n{hint}\n{error}"])
                            ->textInput(['maxlength' => true]) .
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

    <!-- 商品参数相关，由于是B2C商城，无需按照分类来规范，直接用到哪个参数自己填写即可，优点是灵活度高，缺点是分类检索没有了，而且缺乏规范，
    如果是B2B2C那就需要不同分类提前规定好属性，以方便商家规范填写和便于生成前端不同规格参数检索   -->
    <?php
    //标题栏
    $paramHeader = Html::beginTag('thead') .
        Html::beginTag('tr') .
        Html::tag('th', Yii::t('goods_param', 'Name'), ['style' => 'width:20%;color:#999;padding:0 8px 0 0']) .
        Html::tag('th', Yii::t('goods_param', 'Value'), ['style' => 'width:50%;color:#999;padding:0 8px']) .
        Html::tag('th', Yii::t('goods_param', 'Sort'), ['style' => 'width:10%;color:#999;padding:0 8px']) .
        Html::tag('th', '', ['style' => 'width:10%;padding:0 8px']) .
        Html::endTag('tr') .
        Html::endTag('thead');
    //参数单元，主要js加载用
    $paramItem = Html::beginTag('tr') .
        Html::beginTag('td', ['style' => 'width:20%;padding:0 8px 0 0']) . Html::TextInput('paramName[]', '', ['class' => 'form-control']) . Html::endTag('td') .
        Html::beginTag('td', ['style' => 'width:50%;padding:8px']) . Html::textInput('paramValue[]', '', ['class' => 'form-control']) . Html::endTag('td') .
        Html::beginTag('td', ['style' => 'width:10%;padding:8px']) . Html::textInput('paramSort[]', '', ['class' => 'form-control']) . Html::endTag('td') .
        Html::beginTag('td', ['style' => 'width:10%;padding:8px']) . Html::button('<i class="fa fa-trash"></i> ' . Yii::t('goods', 'delete'), ['class' => 'btn btn-xs btn-danger delete_goods_param']) . Html::endTag('td') .
        Html::endTag('tr');
    //整体结构
    $param = Html::beginTag('div', ['class' => 'form-group c-md-8', 'id' => 'goods_param_div']) .
        Html::label(Yii::t('goods', 'Goods Param'), null, ['class' => 'control-label',]) . '<br>' .
        Html::beginTag('table', ['style' => 'width:100%']) .
        $paramHeader .
        Html::beginTag('tbody', ['id' => 'goods_params']);
    if (!empty($model->goodsParams)) {
        foreach ($model->goodsParams as $value) {
            $param .= Html::beginTag('tr') .
                Html::beginTag('td', ['style' => 'width:20%;padding:0 8px 0 0']) . Html::TextInput('paramName[]', $value->name, ['class' => 'form-control']) . Html::endTag('td') .
                Html::beginTag('td', ['style' => 'width:50%;padding:8px']) . Html::textInput('paramValue[]', $value->value, ['class' => 'form-control']) . Html::endTag('td') .
                Html::beginTag('td', ['style' => 'width:10%;padding:8px']) . Html::textInput('paramSort[]', $value->sort, ['class' => 'form-control']) . Html::endTag('td') .
                Html::beginTag('td', ['style' => 'width:10%;padding:8px']) . Html::button('<i class="fa fa-trash"></i> ' . Yii::t('goods', 'delete'), ['class' => 'btn btn-xs btn-danger delete_goods_param']) . Html::endTag('td') .
                Html::endTag('tr');
        }
    }
    $param .= Html::endTag('tbody');
    $param .= Html::endTag('table');
    $param .= Html::endTag('div');
    $param .= Html::button('<i class="fa fa-plus"></i> ' . Yii::t('goods', 'add param'), ['id' => 'add_goods_param', 'class' => 'btn btn-primary']);
    ?>

    <!-- 商品规格，优缺点同商品参数差不多 -->
    <?php
    //相关只读ID
    $goodsSnId = Html::getInputId($model, 'goods_sn');
    $goodsBarcodeId = Html::getInputId($model, 'goods_barcode');
    $weightId = Html::getInputId($model, 'weight');
    $stockId = Html::getInputId($model, 'stock');
    $stockAlarmId = Html::getInputId($model, 'stock_alarm');
    $priceId = Html::getInputId($model, 'price');
    $marketPriceId = Html::getInputId($model, 'market_price');
    $costPriceId = Html::getInputId($model, 'cost_price');
    //开关
    $hasSpec = $form->field($model, 'has_spec', ['options' => ['class' => 'form-group c-md-10']])
        ->hint(Yii::t('goods', 'has_spec_hint'))
        ->widget(SwitchInput::classname(), [
            'pluginOptions' => ['size' => 'small'],
            'pluginEvents' => [
                "switchChange.bootstrapSwitch" => "function(e,status) {
                    if(status){
                        $('#'+'$goodsSnId').attr('readonly',true);
                        $('#'+'$goodsBarcodeId').attr('readonly',true);
                        $('#'+'$weightId').attr('readonly',true);
                        $('#'+'$stockId').attr('readonly',true);
                        $('#'+'$stockAlarmId').attr('readonly',true);
                        $('#'+'$priceId').attr('readonly',true);
                        $('#open_spec').show();
                    }else{
                        $('#'+'$goodsSnId').attr('readonly',false);
                        $('#'+'$goodsBarcodeId').attr('readonly',false);
                        $('#'+'$weightId').attr('readonly',false);
                        $('#'+'$stockId').attr('readonly',false);
                        $('#'+'$stockAlarmId').attr('readonly',false);
                        $('#'+'$priceId').attr('readonly',true);
                        $('#open_spec').hide();
                    }
               }"
            ]
        ]);
    //单个规格
    $specUnit=Html::beginTag('div',['style'=>'padding:8px;margin:5px 0;border:1px dashed #bbb;background:#ecf0f5','class' => 'c-md-9 spec_unit','data-id'=>'SPC-PLACEHOLDER']).
        Html::TextInput('spec[SPC-PLACEHOLDER]','',  ['class' => 'form-control c-md-9 spec_input','data-id'=>'SPC-PLACEHOLDER','style'=>'float:left','placeholder'=>Yii::t('goods','like color and so on')]).
        Html::button('<i class="fa fa-plus"></i> ' . Yii::t('goods', 'add spec item'), ['class' => 'btn btn-xs btn-primary add_spec_item' ,'data-id'=>'SPC-PLACEHOLDER','style'=>'margin:6px 0 0 10px;float:left']).
        Html::button('<i class="fa fa-trash"></i> ' . Yii::t('goods', 'delete'), ['class' => 'btn btn-xs btn-danger delete_spec', 'style'=>'margin:6px 0 0 10px;float:left']).
        Html::tag('div','',['class'=>'spec_item c-md-10','data-id'=>'SPC-PLACEHOLDER']).
        Html::tag('div','',['style'=>'clear:both']).
        Html::endTag('div');
    //单个规格单元
    $specItemUnit=Html::beginTag('div',['style'=>'margin:5px 10px 0 0;float:left;width:31%','class'=>'spec_item_unit','data-id'=>'SPC-ITEM-PLACEHOLDER','data-n'=>'SPC-ITEM-NUM-PLACEHOLDER']).
        Html::input('text','spec_item[SPC-PLACEHOLDER][SPC-ITEM-PLACEHOLDER]','',  ['data-id'=>'SPC-ITEM-PLACEHOLDER','class' => 'form-control c-md-10 spec_item_input','style'=>'float:left']).
        Html::button('<i class="fa fa-close"></i> ', ['class' => 'btn btn-xs btn-danger delete_spec_item', 'style'=>'margin:6px 0 0 3px;float:left']).
        Html::endTag('div');
    //sku标题栏
    $skuHeader = Html::beginTag('thead') .
        Html::beginTag('tr') .
        'EXTEND-THEAD-PLACEHOLDER'.
        Html::tag('th', Yii::t('goods', 'Price'), ['style' => 'color:#999']) .
        Html::tag('th', Yii::t('goods', 'Market Price'), ['style' => 'color:#999']) .
        Html::tag('th', Yii::t('goods', 'Cost Price'), ['style' => 'color:#999']) .
        Html::tag('th', Yii::t('goods', 'Stock'), ['style' => 'color:#999']) .
        Html::tag('th', Yii::t('goods', 'Stock Alarm'), ['style' => 'color:#999']) .
        Html::tag('th', Yii::t('goods', 'Weight'), ['style' => 'color:#999']) .
        Html::tag('th', Yii::t('goods', 'Goods Sn'), ['style' => 'color:#999']) .
        Html::tag('th', Yii::t('goods', 'Goods Barcode'), ['style' => 'color:#999']) .
        Html::endTag('tr') .
        Html::endTag('thead');
    //sku内容
    $skuTbody = Html::beginTag('tr',['style'=>'margin:5px 0']) .
        'EXTEND-TBODY-PLACEHOLDER'.
        Html::beginTag('td') . Html::TextInput('sku[SKU-PLACEHOLDER][price]', '', ['class' => 'form-control sku_input','data-type'=>'price','data-unique_type'=>'SKU-PLACEHOLDER|price']) . Html::endTag('td') .
        Html::beginTag('td') . Html::textInput('sku[SKU-PLACEHOLDER][market_price]', '', ['class' => 'form-control sku_input','data-type'=>'market_price','data-unique_type'=>'SKU-PLACEHOLDER|market_price']) . Html::endTag('td') .
        Html::beginTag('td') . Html::textInput('sku[SKU-PLACEHOLDER][cost_price]', '', ['class' => 'form-control sku_input','data-type'=>'cost_price','data-unique_type'=>'SKU-PLACEHOLDER|cost_price']) . Html::endTag('td') .
        Html::beginTag('td') . Html::TextInput('sku[SKU-PLACEHOLDER][stock]', '', ['class' => 'form-control sku_input','data-type'=>'stock','data-unique_type'=>'SKU-PLACEHOLDER|stock']) . Html::endTag('td') .
        Html::beginTag('td') . Html::textInput('sku[SKU-PLACEHOLDER][stock_alarm]', '', ['class' => 'form-control sku_input','data-type'=>'stock_alarm','data-unique_type'=>'SKU-PLACEHOLDER|stock_alarm']) . Html::endTag('td') .
        Html::beginTag('td') . Html::textInput('sku[SKU-PLACEHOLDER][weight]', '', ['class' => 'form-control sku_input','data-type'=>'weight','data-unique_type'=>'SKU-PLACEHOLDER|weight']) . Html::endTag('td') .
        Html::beginTag('td') . Html::TextInput('sku[SKU-PLACEHOLDER][goods_sn]', '', ['class' => 'form-control sku_input','data-type'=>'goods_sn','data-unique_type'=>'SKU-PLACEHOLDER|goods_sn']) . Html::endTag('td') .
        Html::beginTag('td') . Html::textInput('sku[SKU-PLACEHOLDER][goods_barcode]', '', ['class' => 'form-control sku_input','data-type'=>'goods_barcode','data-unique_type'=>'SKU-PLACEHOLDER|goods_barcode']) . Html::endTag('td') .
        Html::endTag('tr');
    //sku表格
    $sku = Html::beginTag('table', ['class'=>'table table-hover table-bordered table-striped text-nowrap']) .
        $skuHeader .
        Html::beginTag('tbody', ['id' => 'goods_skus']) .
        'TBODY-PLACEHOLDER'.
        Html::endTag('tbody') .
        Html::endTag('table');
    //整体规格展示
    $spec = $hasSpec .
        Html::beginTag('div', ['id' => 'open_spec', 'style' => $model->has_spec ? 'display:block' : 'display:none']) .
        Html::beginTag('div',['id'=>'spec_div','style'=>'margin-bottom:10px']);
        //展示已有的规格
        if(!empty($model->spec_name)){
            $specNameArr=json_decode($model->spec_name,true);
            $specValueArr=json_decode($model->spec_value,true);
            foreach ( $specNameArr as $key=>$value){
                $spec.=Html::beginTag('div',['style'=>'padding:8px;margin:5px 0;border:1px dashed #bbb;background:#ecf0f5','class' => 'c-md-9 spec_unit','data-id'=>$key]).
                    Html::TextInput("spec[$key]",$value,  ['class' => 'form-control c-md-9 spec_input','data-id'=>$key,'style'=>'float:left','placeholder'=>Yii::t('goods','like color and so on')]).
                    Html::button('<i class="fa fa-plus"></i> ' . Yii::t('goods', 'add spec item'), ['class' => 'btn btn-xs btn-primary add_spec_item' ,'data-id'=>$key,'style'=>'margin:6px 0 0 10px;float:left']).
                    Html::button('<i class="fa fa-trash"></i> ' . Yii::t('goods', 'delete'), ['class' => 'btn btn-xs btn-danger delete_spec', 'style'=>'margin:6px 0 0 10px;float:left']).
                    Html::beginTag('div',['class'=>'spec_item c-md-10','data-id'=>$key]);
                    foreach ($specValueArr[$key] as $k=>$v){
                        $spec.=Html::beginTag('div',['style'=>'margin:5px 10px 0 0;float:left;width:31%','class'=>'spec_item_unit','data-id'=>$k,'data-n'=>explode('_',$k)[1]]).
                        Html::input('text',"spec_item[$key][$k]",$v,  ['data-id'=>$k,'class' => 'form-control c-md-10 spec_item_input','style'=>'float:left']).
                        Html::button('<i class="fa fa-close"></i> ', ['class' => 'btn btn-xs btn-danger delete_spec_item', 'style'=>'margin:6px 0 0 3px;float:left']).
                        Html::endTag('div');
                    }
                $spec.=Html::endTag('div').
                    Html::tag('div','',['style'=>'clear:both']).
                    Html::endTag('div');
            }
        }

        $spec.=Html::endTag('div').
        Html::button('<i class="fa fa-plus"></i> ' . Yii::t('goods', 'add spec'), ['id' => 'add_goods_spec', 'class' => 'btn btn-primary']).
        //手动刷新按钮
//        Html::button('<i class="fa fa-refresh"></i> ' . Yii::t('goods', 'refresh sku'), ['id' => 'refresh_sku', 'class' => 'btn btn-primary', 'style'=>'margin-left:10px']).
        Html::beginTag('div',['id'=>'sku_div','class'=>'table-responsive c-md-9','style'=> 'margin-top:10px']).//todo,是否显示的判定
        //展示存在的内容

        Html::endTag('div').
        Html::endTag('div');
    ?>


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
                'label' => Yii::t('goods', 'spec'),
                'content' => $spec
            ],
            [
                'label' => Yii::t('goods', 'param'),
                'content' => $param
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
$imgOthersErrorMsg = Yii::t('goods', 'Image can not empty!');
$js = <<<eof
//红星提示
    $('.required').each(function(){
        var label=$(this).children(':first');
        label.html(label.html()+'<i style="color:red">*</i>');
    });
    //增加商品组图前端js验证
    $('#goods-form').yiiActiveForm('add', {
        id: 'goods-img_others',
        name: 'img_others',
        container: '.field-goods-img_others',
        input: '#goods-img_others',
        error: '.help-block',
        validate:  function (attribute, value, messages, deferred, \$form) {
            if(!$('#img_others').val()){
                yii.validation.addMessage(messages,'$imgOthersErrorMsg');
            }
        }
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
    //后端服务器验证出错后切换标签页，一般用不到
    var errorContainer=$(".has-error:first"),
        tab;
    if(errorContainer.hasClass('field-goods-freight_id') || errorContainer.hasClass('field-goods-freight_price')){
        tab=$(errorContainer).parent().parent().parent().parent();
    }else{
        tab= errorContainer.parent();
    }
    var tabId=tab.attr('id');
    $('a[href="#'+tabId+'"]').tab('show');
    //商品参数增删
    $('#add_goods_param').on('click',function(){
        var html='$paramItem';
        $('#goods_params').append(html);
    })
    $('#goods_param_div').on('click','.delete_goods_param',function(){
        $(this).parent().parent().remove();
    })
    //商品规格增删
    $('#add_goods_spec').on('click',function(){
        //获取当前的最后一个标识，在此基础上加1，没有时为0，需要注意，当编辑时需要按照id从小到大顺序来遍历
        var last=$('.spec_unit:last'),
            i=1;
        if(last.length!==0){
            i=parseInt(last.data('id'))+1;
        }
        var html='$specUnit';
        //替换固定字符串为动态
        html=html.replace(/SPC-PLACEHOLDER/g,i);
        //写入，并增加事件
        $('#spec_div').append(html).find('.spec_input').change(function(){
            refreshSku();
        });
    })
    $('#open_spec').on('click','.delete_spec',function(){
        $(this).parent().remove();
        //如果有值，则刷新
        if($(this).prevAll('.spec_input').first().val()){
            refreshSku();
        }
    })
    //商品规格单元增删
    $('#open_spec').on('click','.add_spec_item',function(){
        //获取当前的最后一个表示，在此基础上加1，没有时为0，需要注意，当编辑时需要按照id从小到大顺序来遍历
        var i=parseInt($(this).data('id'))
        var lastItem=$(this).nextAll().find('.spec_item_unit:last'),
            n=1;
        if(lastItem.length!=0){
            n=parseInt(lastItem.data('n'))+1;
        }
        var j=i+'_'+n;
        var html='$specItemUnit';
        //替换固定字符串为动态
        html=html.replace('SPC-PLACEHOLDER',i);
        html=html.replace('SPC-ITEM-NUM-PLACEHOLDER',n);
        html=html.replace(/SPC-ITEM-PLACEHOLDER/g,j);
        var item=$(this).nextAll('.spec_item').first();
        //写入，并增加事件
        item.append(html).find('.spec_item_input').change(function(){
            refreshSku();
        });
    })
    $('#open_spec').on('click','.delete_spec_item',function(){
        $(this).parent().remove();
        //如果有值，则刷新
        if($(this).prev().first().val()){
            refreshSku();
        }
    })
    //sku字段的value缓存数组
    var SV=[];
    //sku生成刷新
    $('#refresh_sku').on('click',function(){
        refreshSku();
    })
    //刷新sku方法
    function refreshSku(){
         //获取规格名称和值的数组
        var specValueArr=[],
            specItemArr=[];
        $(".spec_input").each(function(){
            if($(this).val()){
                var specId=parseInt($(this).data('id'));
                var index=specItemArr.length;
                specItemArr[index]=[];
                //查询是否有值，只有存在值时才有效
                $(this).nextAll('.spec_item').find('.spec_item_input').each(function(i,v){
                    if($(this).val()){
                        specItemArr[index][i]={'spec_id':specId,'id':$(this).data('id'),'value':$(this).val()};
                    }
                })
                console.log(specItemArr);
                //如果不存在，则去掉此空数组，否则在笛卡尔积时有空的参数，导致出错
                if(specItemArr[index].length==0){
                    specItemArr.splice(index,1);
                }else{
                    //存在，则名称也有效
                    specValueArr.push($(this).val());
                }
            }
        })
       console.log(specItemArr);
       //获取笛卡尔积
       multiSpecItemArr=descartes.apply(this,specItemArr);
       console.log(multiSpecItemArr);
       if(multiSpecItemArr.length!=0){
           //表标题扩增
           var extendThead='';
           specValueArr.forEach(function(value,index,array){
                extendThead+='<th style="color:#999">'+value+'</th>'
           })
           //表内容
           var tbody='';
           multiSpecItemArr.forEach(function(value,index,array){
                //sku_id参数
                var extendTbody='<input type="hidden" name="sku[SKU-PLACEHOLDER][sku_id]" data-type="sku_id" data-unique_type="SKU-PLACEHOLDER|sku_id" value="">';
                //sku字段name下标
                var skuName='';
                //表内容扩增
                value.forEach(function(v,i,arr){
                    extendTbody+='<td style="vertical-align: middle">'+
                    v.value+
                    '<input type="hidden" name="sku[SKU-PLACEHOLDER][value]['+v.id+']" value="'+v.value+'">'+
                    '</td>';
                    if(i==0){
                        skuName+=v.id;
                    }else{
                        skuName+='_'+v.id;
                    }
                })
                var skuTbody='$skuTbody';
                //替换表内容扩增占位
                skuTbody=skuTbody.replace('EXTEND-TBODY-PLACEHOLDER',extendTbody);
                //替换sku内字段name
                skuTbody=skuTbody.replace(/SKU-PLACEHOLDER/g,skuName);
                tbody+=skuTbody;
           })
           //替换表格标题和内容中的占位符
           var sku='$sku';
           sku=sku.replace('EXTEND-THEAD-PLACEHOLDER',extendThead);
           sku=sku.replace('TBODY-PLACEHOLDER',tbody);
           //写入
           $('#sku_div').html(sku)
           .find('.sku_input').each(function(){
                //填充缓存数据
                var type=$(this).data('type');
                var uniqueType=$(this).data('unique_type');
                try{
                    $(this).val(SV[uniqueType]);
                }catch(ex){
                    $(this).val('');
                };
                if (type == 'price' && $(this).val() == '') {
                    $(this).val($('#$priceId').val());
                }
                if (type == 'market_price' && $(this).val() == '') {
                    $(this).val($('#$marketPriceId').val());
                }
                if (type == 'cost_price' && $(this).val() == '') {
                    $(this).val($('#$costPriceId').val());
                }
                if (type == 'weight' && $(this).val() == '') {
                    $(this).val($('#$weightId').val());
                }
                if (type == 'stock' && $(this).val() == '') {
                    $(this).val(0);
                }
                if (type == 'stock_alarm' && $(this).val() == '') {
                    $(this).val(0);
                }
           }).end()
           . find('input[data-type="stock"]').change(function(){
                //库存变动事件
                computeStock();
           }).end()
           .find('input[data-type="price"]').change(function(){
                //价格变动事件
                computePrice();
           }).end()
           .find('.sku_input').change(function(){
                //当值变化时，存储缓存
                var uniqueType=$(this).data('unique_type');
                SV[uniqueType] = $(this).val();
           });
       }else{
         //如果没有，则取消显示
         $('#sku_div').html('');
       }
    }
    //todo,当sku中库存值变化时，重新计算库存之和作为总库存
    function computeStock(){
        
    }
    //todo,当sku中价格变化时，重新获取最低价格作为展示价格
    function computePrice(){
    
    }
    //求笛卡尔积的方法
    function descartes(){
        if( arguments.length == 0 ){
            return [];
        }else if( arguments.length == 1 ){
            var res = []
            arguments[0].forEach(function(v,i){
                res[i] = [];
                res[i].push(v)
            })
            return res;
        }
        return [].reduce.call(arguments, function(col, set) {
            var res = [];
            col.forEach(function(c) {set.forEach(function(s) {
                var t = [].concat( Array.isArray(c) ? c : [c] );
                t.push(s);
                res.push(t);
            })});
            return res;
        });
    }
    
eof;
$this->registerJs($js);
//当更新时，初始js
if (!$model->isNewRecord) {
    if ($model->has_spec == 1) {
        //只读状态
        $showJs = <<<eof
$('#'+'$goodsSnId').attr('readonly',true);
$('#'+'$goodsBarcodeId').attr('readonly',true);
$('#'+'$weightId').attr('readonly',true);
$('#'+'$stockId').attr('readonly',true);
$('#'+'$stockAlarmId').attr('readonly',true);
$('#'+'$priceId').attr('readonly',true);
$('#open_spec').show();
eof;
        //todo,绑定事件
        //todo,sku赋值
        $skuArr=$model->goodsSku;
        foreach ($skuArr as $sku){
            $goodsSpec=json_decode($sku['goods_spec'],true);
//            $unique=
        }
        $this->registerJs($showJs);
    }
}
?>