<?php

use backend\modules\shop\models\GoodsCategory;
use kartik\file\FileInput;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

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

    <?= $form->field($model, 'img', ['options' => ['class' => 'form-group c-md-6']])->hint(Yii::t('goods_category', 'img_hint'))
        ->widget(FileInput::classname(), [
            'options' => [
                'accept' => 'image/*',
                'hiddenOptions'=>['value'=>$model->img]//隐藏字段value
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
                'showUpload' => false,//单图上传，不显示批量上传按钮，否则还要写回调
                'showRemove' => false,//单图上传，不显示移除按钮，否则还要写回调
            ],
            'pluginEvents' => [
                //单个点击上传完毕后给隐藏表单赋值
                'fileuploaded' => new JsExpression("function (event,data){
                        $('input[type=\'hidden\'][name=\'" . Html::getInputName($model, 'img') . "\']').val(data.response.initialPreview[0]);
                       }"),
                //单个点击删除时清空隐藏表单
                'filedeleted' => new JsExpression("function (event,key,jqXHR,data){
                        $('input[type=\'hidden\'][name=\'" . Html::getInputName($model, 'img') . "\']').val('');
                       }"),
            ]

        ]); ?>

    <?= $form->field($model, 'adv_img', ['options' => ['class' => 'form-group c-md-6']])->hint(Yii::t('goods_category', 'adv_img_hint'))
        ->widget(FileInput::classname(), [
            'options' => [
                'accept' => 'image/*',
                'hiddenOptions'=>['value'=>$model->adv_img]//隐藏字段value
            ],
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
                'showUpload' => false,
                'showRemove' => false,
            ],
            'pluginEvents' => [
                //单个点击上传完毕后给隐藏表单赋值
                'fileuploaded' =>  new JsExpression("function (event,data){
                        $('input[type=\'hidden\'][name=\'" . Html::getInputName($model, 'adv_img') . "\']').val(data.response.initialPreview[0]);
                       }"),
                //单个点击删除时清空隐藏表单
                'filedeleted' => new JsExpression("function (event,key,jqXHR,data){
                        $('input[type=\'hidden\'][name=\'" . Html::getInputName($model, 'adv_img') . "\']').val('');
                       }"),
            ]

        ]); ?>

    <?= $form->field($model, 'adv_type', ['options' => ['class' => 'form-group c-md-5']])->widget(Select2::classname(), [
        'data' => GoodsCategory::getAdvTypeOptions(),
        'options' => [
            'encode' => false,
        ]
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
