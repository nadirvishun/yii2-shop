<?php

use backend\modules\shop\models\GoodsCategory;
use kartik\widgets\Select2;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
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

        <?php $goodsSn = $form->field($model, 'goods_sn', ['options' => ['class' => 'form-group c-md-6']])->textInput(['maxlength' => true]) ?>

        <?php $goodsBarcode = $form->field($model, 'goods_barcode', ['options' => ['class' => 'form-group c-md-6']])->textInput(['maxlength' => true]) ?>

        <?php $title = $form->field($model, 'title', ['options' => ['class' => 'form-group c-md-6']])->textInput(['maxlength' => true]) ?>

        <?php $subTitle = $form->field($model, 'sub_title', ['options' => ['class' => 'form-group c-md-6']])->textarea(['rows' => 5]) ?>

        <?php $categoryId = $form->field($model, 'category_id', ['options' => ['class' => 'form-group c-md-6']])->widget(Select2::classname(), [
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

        <?php $price = $form->field($model, 'price', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $unit = $form->field($model, 'unit', ['options' => ['class' => 'form-group c-md-6']])->textInput(['maxlength' => true]) ?>

        <?php $marketPrice = $form->field($model, 'market_price', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $costPrice = $form->field($model, 'cost_price', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $img = $form->field($model, 'img', ['options' => ['class' => 'form-group c-md-6']])->textInput(['maxlength' => true]) ?>

        <?php $imgOther = $form->field($model, 'img_others', ['options' => ['class' => 'form-group c-md-6']])->textarea(['rows' => 6]) ?>

        <?php $content = $form->field($model, 'content', ['options' => ['class' => 'form-group c-md-6']])->textarea(['rows' => 6]) ?>

        <?php $sales = $form->field($model, 'sales', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $stock = $form->field($model, 'stock', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $stockAlarm = $form->field($model, 'stock_alarm', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $stockType = $form->field($model, 'stock_type', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $weight = $form->field($model, 'weight', ['options' => ['class' => 'form-group c-md-6']])->textInput(['maxlength' => true]) ?>

        <?php $isFreightFree = $form->field($model, 'is_freight_free', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $freightType = $form->field($model, 'freight_type', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $freightId = $form->field($model, 'freight_id', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $freightPrice = $form->field($model, 'freight_price', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $isNew = $form->field($model, 'is_new', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $isHot = $form->field($model, 'is_hot', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $isRecommend = $form->field($model, 'is_recommend', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $isLimit = $form->field($model, 'is_limit', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $maxBuy = $form->field($model, 'max_buy', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $minBuy = $form->field($model, 'min_buy', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $userMaxBuy = $form->field($model, 'user_max_buy', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $giveIntegral = $form->field($model, 'give_integral', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $sort = $form->field($model, 'sort', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

        <?php $status = $form->field($model, 'status', ['options' => ['class' => 'form-group c-md-6']])->textInput() ?>

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
                        $isFreightFree .
                        $freightType .
                        $isHot .
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
                    'content' => $img
                ],
                [
                    'label' => Yii::t('goods', 'content'),
                    'content' => $content
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
//增加必填字段红星提示
$js = <<<eof
    $('.required').each(function(){
        var label=$(this).children(':first');
        label.html(label.html()+'<i style="color:red">*</i>');
    });
    $("#goods-form").on('afterValidate',function(event, messages, deferreds){
    })
    
eof;
$this->registerJs($js);
?>