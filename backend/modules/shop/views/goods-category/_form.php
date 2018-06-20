<?php

use backend\modules\shop\models\GoodsCategory;
use kartik\file\FileInput;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\GoodsCategory */
/* @var $form yii\widgets\ActiveForm */
/* @var $treeOptions backend\modules\shop\controllers\GoodsCategoryController */
?>

<div class="goods-category-form">

    <?php $form = ActiveForm::begin([
        'id' => 'goods-category-form',
        'options' => ['class' => 'box-body']
    ]); ?>

    <?= $form->field($model, 'pid', ['options' => ['class' => 'form-group c-md-5']])->widget(Select2::classname(), [
        'data' => $treeOptions,
        'options' => [
            'prompt' => Yii::t('common', 'Please Select...'),
            'encode' => false,
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>
    <?= $form->field($model, 'name', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'img', ['options' => ['class' => 'form-group c-md-5']])->hint(Yii::t('goods_category', 'img_hint'))
        ->widget(FileInput::classname(), [
            'options' => [
                'accept' => 'image/*',
            ],
            'pluginOptions' => [
                'uploadUrl' => Url::to(['upload', 'action' => 'upload']),//ajax上传路径
                'uploadExtraData' => [
                    'name' => Html::getInputName($model, 'img'),
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
                'showUpload' => false,
            ],
            'pluginEvents' => [
                //单个点击上传完毕后给隐藏表单赋值
//            'fileuploaded' => new \yii\web\JsExpression("function (event,data){
//                        var arr=[];
//                        $('.field-goodscategory-img .kv-file-remove').each(function(){
//                            var key=$(this).data('key');
//                            if(key && arr.indexOf(key)=='-1'){
//                                arr.push(key)
//                            }
//                        })
//                        $('input[type=\'hidden\'][name=\'" . Html::getInputName($model, 'img') . "\']').val(arr.join(','));
//                       }"),
            ]

        ]); ?>

    <?= $form->field($model, 'adv_img', ['options' => ['class' => 'form-group c-md-5']])->hint(Yii::t('goods_category', 'adv_img_hint'))
        ->widget(FileInput::classname(), [
            'options' => ['accept' => 'image/*'],
            'pluginOptions' => [
                'uploadUrl' => Url::to(['upload', 'action' => 'upload']),//ajax上传路径
                'uploadExtraData' => [
                    'name' => Html::getInputName($model, 'adv_img'),
                ],
                'showPreview' => true,
                'showClose' => false,
                'initialPreview' => empty($model->adv_img) ? [] : [$model->adv_img],
                'initialPreviewConfig' => [
                    [
                        'caption' => basename($model->adv_img),
                        'url' => Url::to(['upload', 'action' => 'delete']),//ajax删除路径
                        'key' => $model->adv_img
                    ]
                ],
                'initialPreviewAsData' => true,
                'overwriteInitial' => true,//多文件不覆盖原有的，单文件覆盖
            ],
            'pluginEvents' => [
                //单个点击上传完毕后给隐藏表单赋值
                'fileuploaded' => "function (event,data){
                        var arr=[];
                        $('.field-goodscategory-adv_img .kv-file-remove').each(function(){
                            var key=$(this).data('key');
                            if(key && arr.indexOf(key)=='-1'){
                                arr.push(key)
                            }
                        })
                        $('input[type=\'hidden\'][name=\'" . Html::getInputName($model, 'adv_img') . "\']').val(arr.join(','));
                       }",
            ]

        ]); ?>

    <?= $form->field($model, 'adv_type', ['options' => ['class' => 'form-group c-md-5']])->widget(Select2::classname(), [
        'data' => GoodsCategory::getAdvTypeOptions(),
        'options' => [
            'prompt' => Yii::t('common', 'Please Select...'),
            'encode' => false,
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>

    <?= $form->field($model, 'adv_value', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true])
        ->hint(Yii::t('goods_category', 'adv_value_hint')) ?>

    <?= $form->field($model, 'is_recommend', ['options' => ['class' => 'form-group c-md-5']])->widget(SwitchInput::classname(), ['pluginOptions' => ['size' => 'small']]) ?>

    <?= $form->field($model, 'status', ['options' => ['class' => 'form-group c-md-5']])->widget(SwitchInput::classname(), ['pluginOptions' => ['size' => 'small']]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'create') : Yii::t('common', 'update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-warning']) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
