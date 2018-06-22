<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\search\GoodsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-search box box-primary">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'box-body']
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'goods_sn') ?>

    <?= $form->field($model, 'goods_barcode') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'sub_title') ?>

    <?php // echo $form->field($model, 'category_id') ?>

    <?php // echo $form->field($model, 'brand_id') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'unit') ?>

    <?php // echo $form->field($model, 'market_price') ?>

    <?php // echo $form->field($model, 'cost_price') ?>

    <?php // echo $form->field($model, 'img') ?>

    <?php // echo $form->field($model, 'img_others') ?>

    <?php // echo $form->field($model, 'content') ?>

    <?php // echo $form->field($model, 'sales') ?>

    <?php // echo $form->field($model, 'real_sales') ?>

    <?php // echo $form->field($model, 'click') ?>

    <?php // echo $form->field($model, 'collect') ?>

    <?php // echo $form->field($model, 'stock') ?>

    <?php // echo $form->field($model, 'stock_alarm') ?>

    <?php // echo $form->field($model, 'stock_type') ?>

    <?php // echo $form->field($model, 'weight') ?>

    <?php // echo $form->field($model, 'is_freight_free') ?>

    <?php // echo $form->field($model, 'freight_type') ?>

    <?php // echo $form->field($model, 'freight_id') ?>

    <?php // echo $form->field($model, 'freight_price') ?>

    <?php // echo $form->field($model, 'is_new') ?>

    <?php // echo $form->field($model, 'is_hot') ?>

    <?php // echo $form->field($model, 'is_recommend') ?>

    <?php // echo $form->field($model, 'is_limit') ?>

    <?php // echo $form->field($model, 'max_buy') ?>

    <?php // echo $form->field($model, 'min_buy') ?>

    <?php // echo $form->field($model, 'user_max_buy') ?>

    <?php // echo $form->field($model, 'give_integral') ?>

    <?php // echo $form->field($model, 'sort') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
