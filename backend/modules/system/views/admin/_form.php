<?php

use backend\modules\system\models\Admin;
use kartik\widgets\FileInput;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\Admin */
/* @var $form yii\widgets\ActiveForm */
/* @var $act backend\modules\system\controllers\AdminController */
?>

<div class="admin-form">

    <?php $form = ActiveForm::begin([
        'id' => 'admin-form',
        'options' => [
            'class' => 'box-body',
            'enctype' => 'multipart/form-data'
        ]
    ]); ?>

    <?= $form->field($model, 'username', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true]) ?>

    <!--如果是新增或者是修改自身，则需要显示密码-->
    <?php if ($act == 'create' || $act == 'modify'): ?>
        <?php $input = $form->field($model, 'password', ['options' => ['class' => 'form-group c-md-5']])
            ->passwordInput(['maxlength' => true]);
        if ($act == 'modify') {//如果是修改自身，需要加提示
            echo $input->hint('不填写则不更改密码');
        } else {
            echo $input;
        }
        ?>
        <?= $form->field($model, 'passwordRepeat', ['options' => ['class' => 'form-group c-md-5']])->passwordInput(['maxlength' => true]) ?>
    <?php endif; ?>

    <?= $form->field($model, 'email', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mobile', ['options' => ['class' => 'form-group c-md-5']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sex', ['options' => ['class' => 'form-group c-md-5']])->widget(Select2::classname(), [
        'data' => Admin::getSexOptions(),
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>

    <!--  上传头像，只能在修改自身时上传  -->
    <?php if ($act == 'modify'): ?>
        <?= $form->field($model, 'avatar', ['options' => ['class' => 'form-group c-md-5']])->widget(FileInput::classname(), [
            'options' => ['accept' => 'image/*'],
            'pluginOptions' => [
                'showPreview' => true,
                'showClose' => false,
                'showUpload' => false,
                'initialPreview' => empty($model->avatar) ? [] : [$model->avatar],
                'initialPreviewAsData' => true,
            ]
        ]); ?>
    <?php endif; ?>

    <?php if ($act == 'create' || $act == 'update'): ?>
        <?= $form->field($model, 'status', ['options' => ['class' => 'form-group c-md-5']])->widget(SwitchInput::classname(), ['pluginOptions' => ['size' => 'small']]) ?>
    <?php endif; ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'create') : Yii::t('common', 'update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-warning']) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
//增加必填字段红星提示
$js = <<<eof
    $('.required').each(function(){
        var label=$(this).children(':first');
        label.html(label.html()+'<i style="color:red">*</i>');
    });
eof;
$this->registerJs($js);
?>

