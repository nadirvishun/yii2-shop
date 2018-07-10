<?php

use backend\modules\shop\models\GoodsCategory;
use kartik\file\FileInput;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\GoodsBrand */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-brand-form">

    <?php $form = ActiveForm::begin([
        'id' => 'goods-brand-form',
        'options' => ['class' => 'box-body']
    ]); ?>

    <?= $form->field($model, 'name', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'initial', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'category_id', ['options' => ['class' => 'form-group c-md-5']])
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

    <?php
    $imgName=Html::getInputName($model, 'img');
    echo $form->field($model, 'img', ['options' => ['class' => 'form-group c-md-6']])->hint(Yii::t('goods_brand', 'img_hint'))
        ->widget(FileInput::classname(), [
            'options' => [
                'accept' => 'image/*',
                'hiddenOptions'=>['value'=>$model->img]//隐藏字段value
            ],
            'pluginOptions' => [
                'uploadUrl' => Url::to(['upload', 'action' => 'upload']),//ajax上传路径
                'uploadExtraData' => [
                    'name' => $imgName,
                ],
                'showPreview' => true,
                'showClose' => false,
                'initialPreview' => empty($model->img) ? [] : [$model->img],
                'initialPreviewConfig' => [
                    [
                        'caption' => basename($model->img),
                        'url' => Url::to(['upload', 'action' => 'delete']),//ajax删除路径
                        'key' => $model->img
                    ]
                ],
                'initialPreviewAsData' => true,
                'overwriteInitial' => true,//多文件不覆盖原有的，单文件覆盖
                'showUpload' => false,//单图上传，不显示批量上传按钮，否则还要写回调
                'showRemove' => false,//单图上传，不显示移除按钮，否则还要写回调
            ],
            'pluginEvents' => [
                //单个点击上传完毕后给隐藏表单赋值
                'fileuploaded' => new JsExpression("function (event,data){
                        $('input[type=\'hidden\'][name=\'" . $imgName . "\']').val(data.response.initialPreview[0]);
                       }"),
                //单个点击删除时清空隐藏表单
                'filedeleted' => new JsExpression("function (event,key,jqXHR,data){
                        $('input[type=\'hidden\'][name=\'" . $imgName . "\']').val('');
                       }"),
            ]

        ]); ?>

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

    <?= $form->field($model, 'sort', ['options' => ['class' => 'form-group c-md-5']])->textInput() ?>

    <?= $form->field($model, 'is_recommend', ['options' => ['class' => 'form-group c-md-5']])->widget(SwitchInput::classname(), ['pluginOptions' => ['size' => 'small']]) ?>

    <?= $form->field($model, 'status', ['options' => ['class' => 'form-group c-md-5']])->widget(SwitchInput::classname(), ['pluginOptions' => ['size' => 'small']]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common','create') : Yii::t('common','update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php //增加必填字段红星提示
$js = <<<eof
    $(".required").each(function(){
        var label=$(this).children(':first');
        label.html(label.html()+'<i style="color:red">*</i>');
    })
eof;
$this->registerJs($js);
?>
