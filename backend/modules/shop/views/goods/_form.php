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
        Html::tag('td', Yii::t('goods_param', 'Name'), ['style' => 'width:20%;color:#999;padding:0 8px 0 0']) .
        Html::tag('td', Yii::t('goods_param', 'Value'), ['style' => 'width:50%;color:#999;padding:0 8px']) .
        Html::tag('td', Yii::t('goods_param', 'Sort'), ['style' => 'width:10%;color:#999;padding:0 8px']) .
        Html::tag('td', '', ['style' => 'width:10%;padding:0 8px']) .
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
    $model->has_spec=1;
    //相关只读ID
    $goodsSnId = Html::getInputId($model, 'goods_sn');
    $goodsBarcodeId = Html::getInputId($model, 'goods_barcode');
    $weightId = Html::getInputId($model, 'weight');
    $stockId = Html::getInputId($model, 'stock');
    $stockAlarmId = Html::getInputId($model, 'stock_alarm');
    $priceId = Html::getInputId($model, 'price');
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
    $specUnit=Html::beginTag('div',['style'=>'padding:8px;margin:5px 0;border:1px dashed #bbb;background:#eee','class' => 'c-md-9']).
        Html::input('text','spec[]','',  ['class' => 'form-control c-md-9','style'=>'float:left','placeholder'=>Yii::t('goods','like color and so on')]).
        Html::button('<i class="fa fa-plus"></i> ' . Yii::t('goods', 'add spec item'), ['class' => 'btn btn-xs btn-primary add_spec_item' ,'style'=>'margin:6px 0 0 10px;float:left']).
        Html::button('<i class="fa fa-trash"></i> ' . Yii::t('goods', 'delete'), ['class' => 'btn btn-xs btn-danger delete_spec', 'style'=>'margin:6px 0 0 10px;float:left']).
        Html::tag('div','',['style'=>'clear:both']).
        Html::tag('div','',['class'=>'spec_item c-md-10']).
        Html::tag('div','',['style'=>'clear:both']).
        Html::endTag('div');
    //单个规格单元
    $specItemUnit=Html::beginTag('div',['style'=>'margin:5px 10px 0 0;float:left;width:31%']).
        Html::input('text','spec_item[]','',  ['class' => 'form-control c-md-10','style'=>'float:left']).
        Html::button('<i class="fa fa-close"></i> ', ['class' => 'btn btn-xs btn-danger delete_spec_item', 'style'=>'margin:6px 0 0 3px;float:left']).
        Html::endTag('div');
    //sku标题栏
    $skuHeader = Html::beginTag('thead') .
        Html::beginTag('tr') .
        Html::tag('td', Yii::t('goods', 'Price'), ['style' => 'color:#999']) .
        Html::tag('td', Yii::t('goods', 'Market Price'), ['style' => 'color:#999']) .
        Html::tag('td', Yii::t('goods', 'Cost Price'), ['style' => 'color:#999']) .
        Html::tag('td', Yii::t('goods', 'Stock'), ['style' => 'color:#999']) .
        Html::tag('td', Yii::t('goods', 'Stock Alarm'), ['style' => 'color:#999']) .
        Html::tag('td', Yii::t('goods', 'Weight'), ['style' => 'color:#999']) .
        Html::tag('td', Yii::t('goods', 'Goods Sn'), ['style' => 'color:#999']) .
        Html::tag('td', Yii::t('goods', 'Goods Barcode'), ['style' => 'color:#999']) .
        Html::endTag('tr') .
        Html::endTag('thead');

    //sku
    $sku= Html::beginTag('table', ['style' => 'width:100%']) .
        $skuHeader .
        Html::beginTag('tbody', ['id' => 'goods_skus']).

    Html::endTag('tbody').
    Html::endTag('table');

    //todo，商品价格自动以最低的为准
    $spec = $hasSpec .
        Html::beginTag('div', ['id' => 'open_spec', 'style' => $model->has_spec ? 'display:block' : 'display:none']) .
        Html::beginTag('div',['id'=>'spec_div','style'=>'margin-bottom:10px']).
        //todo,展示已有的规格

        Html::endTag('div').
        Html::button('<i class="fa fa-plus"></i> ' . Yii::t('goods', 'add spec'), ['id' => 'add_goods_spec', 'class' => 'btn btn-primary']).
        Html::button('<i class="fa fa-refresh"></i> ' . Yii::t('goods', 'refresh sku'), ['id' => 'refresh_sku', 'class' => 'btn btn-primary', 'style'=>'margin-left:10px']).
        Html::beginTag('div',['style'=>$model->has_spec ? 'display:block' : 'display:none']).//todo,是否显示的判定
        $sku.
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
        var html='$specUnit';
        $('#spec_div').append(html);
    })
    $('#open_spec').on('click','.delete_spec',function(){
        $(this).parent().remove();
    })
    //商品规格单元增删
    $('#open_spec').on('click','.add_spec_item',function(){
        var html='$specItemUnit';
        var item=$(this).next().next().next();
        item.append(html);
    })
    $('#open_spec').on('click','.delete_spec_item',function(){
        $(this).parent().remove();
    })
    //sku生成刷新
    $('#refresh_sku').on('click',function(){
        refreshSku();
    })
    //刷新sku方法
    function refreshSku(){
        //获取规格名称
        //获取规格单元名称
        
    }
    
eof;
$this->registerJs($js);
?>
<script>

  /*  $(function(){
        $(document).on('input propertychange change', '#specs input', function () {
            // 改变规格锁定提交
            window.optionchanged = true;
            $('#optiontip').show();
        });


        $(".spec_item_thumb").find('i').click(function(){
            var group  =$(this).parent();
            group.find('img').attr('src',"../addons/ewei_shopv2/static/images/nopic100.jpg");
            group.find(':hidden').val('');
            $(this).hide();
            group.find('img').popover('destroy');
        });

        require(['jquery.ui'],function(){
            $('#specs').sortable({
                stop: function(){
                    refreshOptions();
                }
            });
            $('.spec_item_items').sortable(
                {
                    handle:'.fa-arrows',
                    stop: function(){
                        refreshOptions();
                    }
                }
            );
        });
        $("#hasoption").click(function(){
            var obj = $(this);
            if (obj.get(0).checked){
                $('#goodssn').attr('readonly',true);
                $('#productsn').attr('readonly',true);
                $('#weight').attr('readonly',true);
                $('#total').attr('readonly',true);

                $("#tboption").show();
                $("#tbdiscount").show();
                $("#isdiscount_discounts").show();
                $("#isdiscount_discounts_default").hide();
                $("#commission").show();
                $("#commission_default").hide();
                $("#discounts_type1").show().parent().show();
                refreshOptions();
            }else{
                $("#tboption").hide();
                refreshOptions();

                $("#isdiscount_discounts").hide();
                var isdiscount_discounts = $("#isdiscount_discounts").html();
                $("#isdiscount_discounts").html('');
                isdiscount_change();
                $("#isdiscount_discounts").html(isdiscount_discounts);

                $("#commission").hide();
                var commission = $("#commission").html();
                $("#commission").html('');
                commission_change();
                $("#commission").html(commission);

                $("#tbdiscount").hide();
                $("#isdiscount_discounts_default").show();

                $("#commission_default").show();

                $('#goodssn').removeAttr('readonly');
                $('#productsn').removeAttr('readonly');

                // 商品类型如果为虚拟卡密则不允许修改库存
                if(type !=3){
                    $('#weight').removeAttr('readonly');
                    $('#total').removeAttr('readonly');
                }
                $("#discounts_type1").hide().parent().hide();
                $("#discounts_type0").click();
            }
        });
    });
    function selectSpecItemImage(obj){
        util.image('',function(val){
            $(obj).attr('src',val.url).popover({
                trigger: 'hover',
                html: true,
                container: $(document.body),
                content: "<img src='" + val.url  + "' style='width:100px;height:100px;' />",
                placement: 'top'
            });

            var group  =$(obj).parent();

            group.find(':hidden').val(val.attachment), group.find('i').show().unbind('click').click(function(){
                $(obj).attr('src',"../addons/ewei_shopv2/static/images/nopic100.jpg");
                group.find(':hidden').val('');
                group.find('i').hide();
                $(obj).popover('destroy');
            });
        });
    }
    function addSpec(){
        var len = $(".spec_item").length;

        if(type==3 && virtual==0 && len>=1){
            tip.msgbox.err('您的商品类型为：虚拟物品(卡密)的多规格形式，只能添加一种规格！');
            return;
        }

        if(type==4 && virtual==0 && len>=2){
            tip.msgbox.err('您的商品类型为：批发商品的多规格形式，只能添加两种规格！');
            return;
        }

        if(type==10 && len>=1){
            tip.msgbox.err('您的商品类型为：话费流量充值，只能添加一种规格！')
            return;
        }

        $("#add-spec").html("正在处理...").attr("disabled", "true").toggleClass("btn-primary");
        var url = "https://i.pin361.com/web/index.php?c=site&a=entry&m=ewei_shopv2&do=web&r=goods.tpl&tpl=spec";
        $.ajax({
            "url": url,
            success:function(data){
                $("#add-spec").html('<i class="fa fa-plus"></i> 添加规格').removeAttr("disabled").toggleClass("btn-primary"); ;
                $('#specs').append(data);
                var len = $(".add-specitem").length -1;
                $(".add-specitem:eq(" +len+ ")").focus();
                refreshOptions();
            }
        });
    }
    function removeSpec(specid){
        if (confirm('确认要删除此规格?')){
            $("#spec_" + specid).remove();
            refreshOptions();
        }
    }
    function addSpecItem(specid){
        $("#add-specitem-" + specid).html("正在处理...").attr("disabled", "true");
        var url = "https://i.pin361.com/web/index.php?c=site&a=entry&m=ewei_shopv2&do=web&r=goods.tpl&tpl=specitem" + "&specid=" + specid;
        $.ajax({
            "url": url,
            success:function(data){
                $("#add-specitem-" + specid).html('<i class="fa fa-plus"></i> 添加规格项').removeAttr("disabled");
                $('#spec_item_' + specid).append(data);
                var len = $("#spec_" + specid + " .spec_item_title").length -1;
                $("#spec_" + specid + " .spec_item_title:eq(" +len+ ")").focus();
                refreshOptions
                if(type==3 && virtual==0){
                    $(".choosetemp").show();
                }
            }
        });
    }
    function removeSpecItem(obj){
        $(obj).closest('.spec_item_item').remove();
        refreshOptions();
    }

    function refreshOptions(){
        // 刷新后重置
        window.optionchanged = false;
        $('#optiontip').hide();


        var html = '<table class="table table-bordered table-condensed"><thead><tr class="active">';
        var specs = [];
        if($('.spec_item').length<=0){
            $("#options").html('');
            $("#discount").html('');
            $("#isdiscount_discounts").html('');
            $("#commission").html('');
            commission_change();
            isdiscount_change();
            return;
        }
        $(".spec_item").each(function(i){
            var _this = $(this);

            var spec = {
                id: _this.find(".spec_id").val(),
                title: _this.find(".spec_title").val()
            };

            var items = [];
            _this.find(".spec_item_item").each(function(){
                var __this = $(this);
                var item = {
                    id: __this.find(".spec_item_id").val(),
                    title: __this.find(".spec_item_title").val(),
                    virtual: __this.find(".spec_item_virtual").val(),
                    show:__this.find(".spec_item_show").get(0).checked?"1":"0"
                }
                items.push(item);
            });
            spec.items = items;
            specs.push(spec);
        });
        specs.sort(function(x,y){
            if (x.items.length > y.items.length){
                return 1;
            }
            if (x.items.length < y.items.length) {
                return -1;
            }
        });

        var len = specs.length;
        var newlen = 1;
        var h = new Array(len);
        var rowspans = new Array(len);
        for(var i=0;i<len;i++){
            html+="<th>" + specs[i].title + "</th>";
            var itemlen = specs[i].items.length;
            if(itemlen<=0) { itemlen = 1 };
            newlen*=itemlen;

            h[i] = new Array(newlen);
            for(var j=0;j<newlen;j++){
                h[i][j] = new Array();
            }
            var l = specs[i].items.length;
            rowspans[i] = 1;
            for(j=i+1;j<len;j++){
                rowspans[i]*= specs[j].items.length;
            }
        }

        /!*商品类型如果为虚拟卡密则不允许修改库存*!/
        if(type==3){
            html += '<th><div class=""><div style="padding-bottom:10px;text-align:center;">库存</div><div class="input-group"><input type="text" class="form-control  input-sm option_stock_all" readonly="readonly" VALUE=""/><span class="input-group-addon disabled"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置"></a></span></div></div></th>';
        }else{
            html += '<th><div class=""><div style="padding-bottom:10px;text-align:center;">库存</div><div class="input-group"><input type="text" class="form-control  input-sm option_stock_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_stock\');"></a></span></div></div></th>';
        }

        html += '<th class="type-4"><div class=""><div style="padding-bottom:10px;text-align:center;">预售价</div><div class="input-group"><input type="text" class="form-control  input-sm option_presell_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_presell\');"></a></span></div></div></th>';
        html += '<th class="type-4"><div class=""><div style="padding-bottom:10px;text-align:center;">现价</div><div class="input-group"><input type="text" class="form-control  input-sm option_marketprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_marketprice\');"></a></span></div></div></th>';
        html+='<th class="type-4"><div class=""><div style="padding-bottom:10px;text-align:center;">原价</div><div class="input-group"><input type="text" class="form-control  input-sm option_productprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_productprice\');"></a></span></div></div></th>';
        html+='<th class="type-4"><div class=""><div style="padding-bottom:10px;text-align:center;">成本价</div><div class="input-group"><input type="text" class="form-control  input-sm option_costprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_costprice\');"></a></span></div></div></th>';
        html+='<th><div class=""><div style="padding-bottom:10px;text-align:center;">编码</div><div class="input-group"><input type="text" class="form-control  input-sm option_goodssn_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_goodssn\');"></a></span></div></div></th>';
        html+='<th><div class=""><div style="padding-bottom:10px;text-align:center;">条码</div><div class="input-group"><input type="text" class="form-control  input-sm option_productsn_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_productsn\');"></a></span></div></div></th>';
        html+='<th><div class=""><div style="padding-bottom:10px;text-align:center;">重量（克）</div><div class="input-group"><input type="text" class="form-control  input-sm option_weight_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_weight\');"></a></span></div></div></th>';
        html+='</tr></thead>';

        for(var m=0;m<len;m++){
            var k = 0,kid = 0,n=0;
            for(var j=0;j<newlen;j++){
                var rowspan = rowspans[m];
                if( j % rowspan==0){
                    h[m][j]={title: specs[m].items[kid].title, virtual: specs[m].items[kid].virtual,html: "<td class='full' rowspan='" +rowspan + "'>"+ specs[m].items[kid].title+"</td>\r\n",id: specs[m].items[kid].id};
                }
                else{
                    h[m][j]={title:specs[m].items[kid].title,virtual: specs[m].items[kid].virtual, html: "",id: specs[m].items[kid].id};
                }
                n++;
                if(n==rowspan){
                    kid++; if(kid>specs[m].items.length-1) { kid=0; }
                    n=0;
                }
            }
        }

        var hh = "";
        for(var i=0;i<newlen;i++){
            hh+="<tr>";
            var ids = [];
            var titles = [];
            var virtuals = [];
            for(var j=0;j<len;j++){
                hh+=h[j][i].html;
                ids.push( h[j][i].id);
                titles.push( h[j][i].title);
                virtuals.push( h[j][i].virtual);
            }

            var sortarr  = permute([],ids);
            titles= titles.join('+');
            ids = ids.join('_');
            var val ={ id : "",title:titles, stock : "",presell : "",costprice : "",productprice : "",marketprice : "",weight:"",productsn:"",goodssn:"",virtual:virtuals };
            for(var kkk=0;kkk<sortarr.length;kkk++) {
                var sids = sortarr[kkk].join('_');
                if ($(".option_id_" + sids).length > 0) {
                    val = {
                        id: $(".option_id_" + sids + ":eq(0)").val(),
                        title: titles,
                        stock: $(".option_stock_" + sids + ":eq(0)").val(),
                        presell: $(".option_presell_" + sids + ":eq(0)").val(),
                        costprice: $(".option_costprice_" + sids + ":eq(0)").val(),
                        productprice: $(".option_productprice_" + sids + ":eq(0)").val(),
                        marketprice: $(".option_marketprice_" + sids + ":eq(0)").val(),
                        goodssn: $(".option_goodssn_" + sids + ":eq(0)").val(),
                        productsn: $(".option_productsn_" + sids + ":eq(0)").val(),
                        weight: $(".option_weight_" + sids + ":eq(0)").val(),
                        virtual: virtuals
                    }
                    break;
                }
            }
            hh += '<td>'
            //  商品类型如果为虚拟卡密则不允许修改库存
            if(type==3){
                hh += '<input data-name="option_stock_' + ids +'" type="text" class="form-control option_stock option_stock_' + ids +'" readonly="readonly"  value=""/></td>';
            }else{
                hh += '<input data-name="option_stock_' + ids +'" type="text" class="form-control option_stock option_stock_' + ids +'" value="' +(val.stock=='undefined'?'':val.stock )+'"/></td>';
            }
            hh += '<input data-name="option_id_' + ids+'" type="hidden" class="form-control option_id option_id_' + ids +'" value="' +(val.id=='undefined'?'':val.id )+'"/>';
            hh += '<input data-name="option_ids" type="hidden" class="form-control option_ids option_ids_' + ids +'" value="' + ids +'"/>';
            hh += '<input data-name="option_title_' + ids +'" type="hidden" class="form-control option_title option_title_' + ids +'" value="' +(val.title=='undefined'?'':val.title )+'"/></td>';
            hh += '<input data-name="option_virtual_' + ids +'" type="hidden" class="form-control option_virtual option_virtual_' + ids +'" value="' +(val.virtual=='undefined'?'':val.virtual )+'"/></td>';
            hh += '</td>';
            hh += '<td class="type-4"><input data-name="option_presell_' + ids+'" type="text" class="form-control option_presell option_presell_' + ids +'" value="' +(val.presell=='undefined'?'':val.presell )+'"/></td>';
            hh += '<td class="type-4"><input data-name="option_marketprice_' + ids+'" type="text" class="form-control option_marketprice option_marketprice_' + ids +'" value="' +(val.marketprice=='undefined'?'':val.marketprice )+'"/></td>';
            hh += '<td class="type-4"><input data-name="option_productprice_' + ids+'" type="text" class="form-control option_productprice option_productprice_' + ids +'" " value="' +(val.productprice=='undefined'?'':val.productprice )+'"/></td>';
            hh += '<td class="type-4"><input data-name="option_costprice_' +ids+'" type="text" class="form-control option_costprice option_costprice_' + ids +'" " value="' +(val.costprice=='undefined'?'':val.costprice )+'"/></td>';
            hh += '<td><input data-name="option_goodssn_' +ids+'" type="text" class="form-control option_goodssn option_goodssn_' + ids +'" " value="' +(val.goodssn=='undefined'?'':val.goodssn )+'"/></td>';
            hh += '<td><input data-name="option_productsn_' +ids+'" type="text" class="form-control option_productsn option_productsn_' + ids +'" " value="' +(val.productsn=='undefined'?'':val.productsn )+'"/></td>';
            hh += '<td><input data-name="option_weight_' + ids +'" type="text" class="form-control option_weight option_weight_' + ids +'" " value="' +(val.weight=='undefined'?'':val.weight )+'"/></td>';
            hh += "</tr>";
        }
        html+=hh;
        html+="</table>";
        $("#options").html(html);
        refreshDiscount();
        refreshIsDiscount();
        refreshCommission();
        commission_change();
        isdiscount_change();

        if(window.type=='4'){
            $('.type-4').hide();
        }else{
            $('.type-4').show();
        }
    }
    function permute(temArr,testArr){
        var permuteArr=[];
        var arr = testArr;
        function innerPermute(temArr){
            for(var i=0,len=arr.length; i<len; i++) {
                if(temArr.length == len - 1) {
                    if(temArr.indexOf(arr[i]) < 0) {
                        permuteArr.push(temArr.concat(arr[i]));
                    }
                    continue;
                }
                if(temArr.indexOf(arr[i]) < 0) {
                    innerPermute(temArr.concat(arr[i]));
                }
            }
        }
        innerPermute(temArr);
        return permuteArr;
    }
    function refreshDiscount() {
        var html = '<table class="table table-bordered table-condensed"><thead><tr class="active">';
        var specs = [];

        $(".spec_item").each(function (i) {
            var _this = $(this);

            var spec = {
                id: _this.find(".spec_id").val(),
                title: _this.find(".spec_title").val()
            };

            var items = [];
            _this.find(".spec_item_item").each(function () {
                var __this = $(this);
                var item = {
                    id: __this.find(".spec_item_id").val(),
                    title: __this.find(".spec_item_title").val(),
                    virtual: __this.find(".spec_item_virtual").val(),
                    show: __this.find(".spec_item_show").get(0).checked ? "1" : "0"
                }
                items.push(item);
            });
            spec.items = items;
            specs.push(spec);
        });
        specs.sort(function (x, y) {
            if (x.items.length > y.items.length) {
                return 1;
            }
            if (x.items.length < y.items.length) {
                return -1;
            }
        });

        var len = specs.length;
        var newlen = 1;
        var h = new Array(len);
        var rowspans = new Array(len);
        for (var i = 0; i < len; i++) {
            html += "<th>" + specs[i].title + "</th>";
            var itemlen = specs[i].items.length;
            if (itemlen <= 0) {
                itemlen = 1
            }
            ;
            newlen *= itemlen;

            h[i] = new Array(newlen);
            for (var j = 0; j < newlen; j++) {
                h[i][j] = new Array();
            }
            var l = specs[i].items.length;
            rowspans[i] = 1;
            for (j = i + 1; j < len; j++) {
                rowspans[i] *= specs[j].items.length;
            }
        }

        html += '<th><div class=""><div style="padding-bottom:10px;text-align:center;">默认会员</div><div class="input-group"><input type="text" class="form-control  input-sm discount_default_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'discount_default\');"></a></span></div></div></th>';
        html += '</tr></thead>';

        for (var m = 0; m < len; m++) {
            var k = 0, kid = 0, n = 0;
            for (var j = 0; j < newlen; j++) {
                var rowspan = rowspans[m];
                if (j % rowspan == 0) {
                    h[m][j] = {
                        title: specs[m].items[kid].title,
                        virtual: specs[m].items[kid].virtual,
                        html: "<td class='full' rowspan='" + rowspan + "'>" + specs[m].items[kid].title + "</td>\r\n",
                        id: specs[m].items[kid].id
                    };
                }
                else {
                    h[m][j] = {
                        title: specs[m].items[kid].title,
                        virtual: specs[m].items[kid].virtual,
                        html: "",
                        id: specs[m].items[kid].id
                    };
                }
                n++;
                if (n == rowspan) {
                    kid++;
                    if (kid > specs[m].items.length - 1) {
                        kid = 0;
                    }
                    n = 0;
                }
            }
        }

        var hh = "";
        for (var i = 0; i < newlen; i++) {
            hh += "<tr>";
            var ids = [];
            var titles = [];
            var virtuals = [];
            for (var j = 0; j < len; j++) {
                hh += h[j][i].html;
                ids.push(h[j][i].id);
                titles.push(h[j][i].title);
                virtuals.push(h[j][i].virtual);
            }
            ids = ids.join('_');
            titles = titles.join('+');
            var val = {
                id: "",
                title: titles,
                leveldefault: '',
                costprice: "",
                presell: "",
                productprice: "",
                marketprice: "",
                weight: "",
                productsn: "",
                goodssn: "",
                virtual: virtuals
            };

            var val ={ id : "",title:titles, leveldefault: '',costprice : "",productprice : "",marketprice : "",weight:"",productsn:"",goodssn:"",virtual:virtuals };
            if ($(".discount_id_" + ids).length > 0) {
                val = {
                    id: $(".discount_id_" + ids + ":eq(0)").val(),
                    title: titles,
                    leveldefault: $(".discount_default_" + ids + ":eq(0)").val(),
                    costprice: $(".discount_costprice_" + ids + ":eq(0)").val(),
                    presell: $(".discount_presell_" + ids + ":eq(0)").val(),
                    productprice: $(".discount_productprice_" + ids + ":eq(0)").val(),
                    marketprice: $(".discount_marketprice_" + ids + ":eq(0)").val(),
                    presell: $(".discount_presell_" + ids + ":eq(0)").val(),
                    goodssn: $(".discount_goodssn_" + ids + ":eq(0)").val(),
                    productsn: $(".discount_productsn_" + ids + ":eq(0)").val(),
                    weight: $(".discount_weight_" + ids + ":eq(0)").val(),
                    virtual: virtuals
                }
            }

            hh += '<td>'
            hh += '<input data-name="discount_level_default_' + ids +'"type="text" class="form-control discount_default discount_default_' + ids +'" value="' +(val.leveldefault=='undefined'?'':val.leveldefault )+'"/>';
            hh += '</td>';
            hh += '<input data-name="discount_id_' + ids+'"type="hidden" class="form-control discount_id discount_id_' + ids +'" value="' +(val.id=='undefined'?'':val.id )+'"/>';
            hh += '<input data-name="discount_ids"type="hidden" class="form-control discount_ids discount_ids_' + ids +'" value="' + ids +'"/>';
            hh += '<input data-name="discount_title_' + ids +'"type="hidden" class="form-control discount_title discount_title_' + ids +'" value="' +(val.title=='undefined'?'':val.title )+'"/></td>';
            hh += '<input data-name="discount_virtual_' + ids +'"type="hidden" class="form-control discount_virtual discount_virtual_' + ids +'" value="' +(val.virtual=='undefined'?'':val.virtual )+'"/></td>';
            hh += "</tr>";
        }
        html += hh;
        html += "</table>";
        $("#discount").html(html);
    }

    function refreshIsDiscount() {
        var html = '<table class="table table-bordered table-condensed"><thead><tr class="active">';
        var specs = [];

        $(".spec_item").each(function (i) {
            var _this = $(this);

            var spec = {
                id: _this.find(".spec_id").val(),
                title: _this.find(".spec_title").val()
            };

            var items = [];
            _this.find(".spec_item_item").each(function () {
                var __this = $(this);
                var item = {
                    id: __this.find(".spec_item_id").val(),
                    title: __this.find(".spec_item_title").val(),
                    virtual: __this.find(".spec_item_virtual").val(),
                    show: __this.find(".spec_item_show").get(0).checked ? "1" : "0"
                }
                items.push(item);
            });
            spec.items = items;
            specs.push(spec);
        });
        specs.sort(function (x, y) {
            if (x.items.length > y.items.length) {
                return 1;
            }
            if (x.items.length < y.items.length) {
                return -1;
            }
        });

        var len = specs.length;
        var newlen = 1;
        var h = new Array(len);
        var rowspans = new Array(len);
        for (var i = 0; i < len; i++) {
            html += "<th>" + specs[i].title + "</th>";
            var itemlen = specs[i].items.length;
            if (itemlen <= 0) {
                itemlen = 1
            }
            ;
            newlen *= itemlen;

            h[i] = new Array(newlen);
            for (var j = 0; j < newlen; j++) {
                h[i][j] = new Array();
            }
            var l = specs[i].items.length;
            rowspans[i] = 1;
            for (j = i + 1; j < len; j++) {
                rowspans[i] *= specs[j].items.length;
            }
        }

        html += '<th><div class=""><div style="padding-bottom:10px;text-align:center;">默认会员</div><div class="input-group"><input type="text" class="form-control  input-sm isdiscount_discounts_default_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'isdiscount_discounts_default\');"></a></span></div></div></th>';
        html += '</tr></thead>';

        for (var m = 0; m < len; m++) {
            var k = 0, kid = 0, n = 0;
            for (var j = 0; j < newlen; j++) {
                var rowspan = rowspans[m];
                if (j % rowspan == 0) {
                    h[m][j] = {
                        title: specs[m].items[kid].title,
                        virtual: specs[m].items[kid].virtual,
                        html: "<td class='full' rowspan='" + rowspan + "'>" + specs[m].items[kid].title + "</td>\r\n",
                        id: specs[m].items[kid].id
                    };
                }
                else {
                    h[m][j] = {
                        title: specs[m].items[kid].title,
                        virtual: specs[m].items[kid].virtual,
                        html: "",
                        id: specs[m].items[kid].id
                    };
                }
                n++;
                if (n == rowspan) {
                    kid++;
                    if (kid > specs[m].items.length - 1) {
                        kid = 0;
                    }
                    n = 0;
                }
            }
        }

        var hh = "";
        for (var i = 0; i < newlen; i++) {
            hh += "<tr>";
            var ids = [];
            var titles = [];
            var virtuals = [];
            for (var j = 0; j < len; j++) {
                hh += h[j][i].html;
                ids.push(h[j][i].id);
                titles.push(h[j][i].title);
                virtuals.push(h[j][i].virtual);
            }
            ids = ids.join('_');
            titles = titles.join('+');
            var val = {
                id: "",
                title: titles,
                leveldefault: '',
                costprice: "",
                presell: "",
                productprice: "",
                marketprice: "",
                weight: "",
                productsn: "",
                goodssn: "",
                virtual: virtuals
            };

            var val ={ id : "",title:titles, leveldefault: '',costprice : "",productprice : "",marketprice : "",weight:"",productsn:"",goodssn:"",virtual:virtuals };
            if ($(".isdiscount_discounts_id_" + ids).length > 0) {
                val = {
                    id: $(".isdiscount_discounts_id_" + ids + ":eq(0)").val(),
                    title: titles,
                    leveldefault: $(".isdiscount_discounts_default_" + ids + ":eq(0)").val(),
                    costprice: $(".isdiscount_discounts_costprice_" + ids + ":eq(0)").val(),
                    productprice: $(".isdiscount_discounts_productprice_" + ids + ":eq(0)").val(),
                    marketprice: $(".isdiscount_discounts_marketprice_" + ids + ":eq(0)").val(),
                    presell: $(".isdiscount_discounts_presell_" + ids + ":eq(0)").val(),
                    goodssn: $(".isdiscount_discounts_goodssn_" + ids + ":eq(0)").val(),
                    productsn: $(".isdiscount_discounts_productsn_" + ids + ":eq(0)").val(),
                    weight: $(".isdiscount_discounts_weight_" + ids + ":eq(0)").val(),
                    virtual: virtuals
                }
            }

            hh += '<td>'
            hh += '<input data-name="isdiscount_discounts_level_default_' + ids +'"type="text" class="form-control isdiscount_discounts_default isdiscount_discounts_default_' + ids +'" value="' +(val.leveldefault=='undefined'?'':val.leveldefault )+'"/>';
            hh += '</td>';
            hh += '<input data-name="isdiscount_discounts_id_' + ids+'"type="hidden" class="form-control isdiscount_discounts_id isdiscount_discounts_id_' + ids +'" value="' +(val.id=='undefined'?'':val.id )+'"/>';
            hh += '<input data-name="isdiscount_discounts_ids"type="hidden" class="form-control isdiscount_discounts_ids isdiscount_discounts_ids_' + ids +'" value="' + ids +'"/>';
            hh += '<input data-name="isdiscount_discounts_title_' + ids +'"type="hidden" class="form-control isdiscount_discounts_title isdiscount_discounts_title_' + ids +'" value="' +(val.title=='undefined'?'':val.title )+'"/></td>';
            hh += '<input data-name="isdiscount_discounts_virtual_' + ids +'"type="hidden" class="form-control isdiscount_discounts_virtual isdiscount_discounts_virtual_' + ids +'" value="' +(val.virtual=='undefined'?'':val.virtual )+'"/></td>';
            hh += "</tr>";
        }
        html += hh;
        html += "</table>";
        $("#isdiscount_discounts").html(html);
    }

    function refreshCommission() {
        var commission_level = [{"key":"default","levelname":"\u9ed8\u8ba4\u7b49\u7ea7"}];
        var html = '<table class="table table-bordered table-condensed"><thead><tr class="active">';
        var specs = [];

        $(".spec_item").each(function (i) {
            var _this = $(this);

            var spec = {
                id: _this.find(".spec_id").val(),
                title: _this.find(".spec_title").val()
            };

            var items = [];
            _this.find(".spec_item_item").each(function () {
                var __this = $(this);
                var item = {
                    id: __this.find(".spec_item_id").val(),
                    title: __this.find(".spec_item_title").val(),
                    virtual: __this.find(".spec_item_virtual").val(),
                    show: __this.find(".spec_item_show").get(0).checked ? "1" : "0"
                }
                items.push(item);
            });
            spec.items = items;
            specs.push(spec);
        });
        specs.sort(function (x, y) {
            if (x.items.length > y.items.length) {
                return 1;
            }
            if (x.items.length < y.items.length) {
                return -1;
            }
        });

        var len = specs.length;
        var newlen = 1;
        var h = new Array(len);
        var rowspans = new Array(len);
        for (var i = 0; i < len; i++) {
            html += "<th>" + specs[i].title + "</th>";
            var itemlen = specs[i].items.length;
            if (itemlen <= 0) {
                itemlen = 1
            }
            ;
            newlen *= itemlen;

            h[i] = new Array(newlen);
            for (var j = 0; j < newlen; j++) {
                h[i][j] = new Array();
            }
            var l = specs[i].items.length;
            rowspans[i] = 1;
            for (j = i + 1; j < len; j++) {
                rowspans[i] *= specs[j].items.length;
            }
        }

        $.each(commission_level,function (key,level) {
            html += '<th><div class=""><div style="padding-bottom:10px;text-align:center;">'+level.levelname+'</div></div></th>';
        })
        html += '</tr></thead>';

        for (var m = 0; m < len; m++) {
            var k = 0, kid = 0, n = 0;
            for (var j = 0; j < newlen; j++) {
                var rowspan = rowspans[m];
                if (j % rowspan == 0) {
                    h[m][j] = {
                        title: specs[m].items[kid].title,
                        virtual: specs[m].items[kid].virtual,
                        html: "<td class='full' rowspan='" + rowspan + "'>" + specs[m].items[kid].title + "</td>\r\n",
                        id: specs[m].items[kid].id
                    };
                }
                else {
                    h[m][j] = {
                        title: specs[m].items[kid].title,
                        virtual: specs[m].items[kid].virtual,
                        html: "",
                        id: specs[m].items[kid].id
                    };
                }
                n++;
                if (n == rowspan) {
                    kid++;
                    if (kid > specs[m].items.length - 1) {
                        kid = 0;
                    }
                    n = 0;
                }
            }
        }
        var hh = "";
        for (var i = 0; i < newlen; i++) {
            hh += "<tr>";
            var ids = [];
            var titles = [];
            var virtuals = [];
            for (var j = 0; j < len; j++) {
                hh += h[j][i].html;
                ids.push(h[j][i].id);
                titles.push(h[j][i].title);
                virtuals.push(h[j][i].virtual);
            }
            ids = ids.join('_');
            titles = titles.join('+');

            var val = {
                id: "",
                title: titles,
                leveldefault: '',
                costprice: "",
                presell: "",
                productprice: "",
                marketprice: "",
                weight: "",
                productsn: "",
                goodssn: "",
                virtual: virtuals
            };

            var val ={ id : "",title:titles, leveldefault: '',costprice : "",productprice : "",marketprice : "",weight:"",productsn:"",goodssn:"",virtual:virtuals };
            var leveldefault = new Array(3);
            $(".commission_default_"+ ids).each(function(index,val){
                leveldefault[index] = val;
            })
            if ($(".commission_id_" + ids).length > 0) {
                val = {
                    id: $(".commission_id_" + ids + ":eq(0)").val(),
                    title: titles,
                    costprice: $(".commission_costprice_" + ids + ":eq(0)").val(),
                    presell: $(".commission_presell_" + ids + ":eq(0)").val(),
                    productprice: $(".commission_productprice_" + ids + ":eq(0)").val(),
                    marketprice: $(".commission_marketprice_" + ids + ":eq(0)").val(),
                    goodssn: $(".commission_goodssn_" + ids + ":eq(0)").val(),
                    productsn: $(".commission_productsn_" + ids + ":eq(0)").val(),
                    weight: $(".commission_weight_" + ids + ":eq(0)").val(),
                    virtual: virtuals
                }
            }
            hh += '<td>';
            var level_temp = leveldefault;
            if (len >= i && typeof (level_temp) != 'undefined')
            {
                if('default' == 'default')
                {
                    for (var li = 0; li<2;li++)
                    {
                        if (typeof (level_temp[li])!= "undefined")
                        {
                            hh += '<input data-name="commission_level_default_' +ids+ '"  type="text" class="form-control commission_default commission_default_' +ids+ '" value="' +$(level_temp[li]).val()+ '" style="display:inline;width: '+96/parseInt(2)+'%;"/> ';
                        }
                        else
                        {
                            hh += '<input data-name="commission_level_default_' +ids+ '"  type="text" class="form-control commission_default commission_default_' +ids+ '" value="" style="display:inline;width: '+96/parseInt(2)+'%;"/> ';
                        }
                    }
                }
                else
                {
                    for (var li = 0; li<2;li++)
                    {
                        if (typeof (level_temp[li])!= "undefined")
                        {
                            hh += '<input data-name="commission_level__' +ids+ '"  type="text" class="form-control commission_level commission_level_' +ids+ '" value="' +$(level_temp[li]).val()+ '" style="display:inline;width: '+96/parseInt(2)+'%;"/> ';
                        }
                        else
                        {
                            hh += '<input data-name="commission_level__' +ids+ '"  type="text" class="form-control commission_level commission_level_' +ids+ '" value="" style="display:inline;width: '+96/parseInt(2)+'%;"/> ';
                        }
                    }
                }
            }
            else
            {
                if('default' == 'default')
                {
                    for (var li = 0; li<2;li++)
                    {
                        if (typeof (level_temp[li])!= "undefined")
                        {
                            hh += '<input data-name="commission_level_default_' +ids+ '"  type="text" class="form-control commission_default commission_default_' +ids+ '" value="' +$(level_temp[li]).val()+ '" style="display:inline;width: '+96/parseInt(2)+'%;"/> ';
                        }
                        else
                        {
                            hh += '<input data-name="commission_level_default_' +ids+ '"  type="text" class="form-control commission_default commission_default_' +ids+ '" value="" style="display:inline;width: '+96/parseInt(2)+'%;"/> ';
                        }
                    }
                }
                else
                {
                    for (var li = 0; li<2;li++)
                    {
                        if (typeof (level_temp[li])!= "undefined")
                        {
                            hh += '<input data-name="commission_level__' +ids+ '"  type="text" class="form-control commission_level commission_level_' +ids+ '" value="' +$(level_temp[li]).val()+ '" style="display:inline;width: '+96/parseInt(2)+'%;"/> ';
                        }
                        else
                        {
                            hh += '<input data-name="commission_level__' +ids+ '"  type="text" class="form-control commission_level commission_level_' +ids+ '" value="" style="display:inline;width: '+96/parseInt(2)+'%;"/> ';
                        }
                    }
                }
            }
            hh += '</td>';
            hh += '<input data-name="commission_id_' + ids+'"type="hidden" class="form-control commission_id commission_id_' + ids +'" value="' +(val.id=='undefined'?'':val.id )+'"/>';
            hh += '<input data-name="commission_ids"type="hidden" class="form-control commission_ids commission_ids_' + ids +'" value="' + ids +'"/>';
            hh += '<input data-name="commission_title_' + ids +'"type="hidden" class="form-control commission_title commission_title_' + ids +'" value="' +(val.title=='undefined'?'':val.title )+'"/></td>';
            hh += '<input data-name="commission_virtual_' + ids +'"type="hidden" class="form-control commission_virtual commission_virtual_' + ids +'" value="' +(val.virtual=='undefined'?'':val.virtual )+'"/></td>';
            hh += "</tr>";
        }
        html += hh;
        html += "</table>";
        $("#commission").html(html);
    }

    function setCol(cls){
        $("."+cls).val( $("."+cls+"_all").val());
    }
    function showItem(obj){
        var show = $(obj).get(0).checked?"1":"0";
        $(obj).parents('.spec_item_item').find('.spec_item_show:eq(0)').val(show);
    }
    function nofind(){
        var img=event.srcElement;
        img.src="./resource/image/module-nopic-small.jpg";
        img.onerror=null;
    }

    function choosetemp(id){
        $('#modal-module-chooestemp').modal();
        $('#modal-module-chooestemp').data("temp",id);
    }
    function addtemp(){
        var id = $('#modal-module-chooestemp').data("temp");
        var temp_id = $('#modal-module-chooestemp').find("select").val();
        var temp_name = $('#modal-module-chooestemp option[value='+temp_id+']').text();
        //alert(temp_id+":"+temp_name);
        $("#temp_name_"+id).val(temp_name);
        $("#temp_id_"+id).val(temp_id);
        $('#modal-module-chooestemp .close').click();
        refreshOptions()
    }

    function setinterval(type)
    {
        var intervalfloor =$('#intervalfloor').val();
        if(intervalfloor=="")
        {
            intervalfloor=0;
        }
        intervalfloor = parseInt(intervalfloor);

        if(type=='plus')
        {
            if(intervalfloor==3)
            {
                tip.msgbox.err("最多添加三个区间价格");
                return;
            }
            intervalfloor=intervalfloor+1;
        }
        else if(type=='minus')
        {
            if(intervalfloor==0)
            {
                tip.msgbox.err("请最少添加一个区间价格");
                return;
            }
            intervalfloor=intervalfloor-1;
        }else
        {
            return;
        }

        if(intervalfloor<1)
        {

            $('#interval1').hide();
            $('#intervalnum1').val("");
            $('#intervalprice1').val("");
        }else
        {
            $('#interval1').show();
        }

        if(intervalfloor<2)
        {

            $('#interval2').hide();
            $('#intervalnum2').val("");
            $('#intervalprice2').val("");
        }else
        {
            $('#interval2').show();
        }

        if(intervalfloor<3)
        {

            $('#interval3').hide();
            $('#intervalnum3').val("");
            $('#intervalprice3').val("");
        }else
        {
            $('#interval3').show();
        }


        $('#intervalfloor').val(intervalfloor);

    }*/



</script>