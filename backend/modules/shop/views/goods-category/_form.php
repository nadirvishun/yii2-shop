<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\GoodsCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-category-form">

    <?php $form = ActiveForm::begin([
        'id' => 'goods-category-form',
        'options' => ['class' => 'box-body']
    ]); ?>

    <?= $form->field($model, 'pid', ['options' => ['class' => 'form-group c-md-5']])->widget(Select2::classname(), [
        'data' => ['0'=>'顶级分类','1'=>'test'],
        'options' => [
            'prompt' => Yii::t('common', 'Please Select...'),
            'encode' => false,
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>
    <?= $form->field($model, 'name', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'img', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'adv_img', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'adv_type', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'adv_value', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sort', ['options' => ['class' => 'form-group c-md-5']])->textInput() ?>

    <?= $form->field($model, 'is_recommend', ['options' => ['class' => 'form-group c-md-5']])->textInput() ?>

    <?= $form->field($model, 'status', ['options' => ['class' => 'form-group c-md-5']])->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common','create') : Yii::t('common','update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-warning']) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
