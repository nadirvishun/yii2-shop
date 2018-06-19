<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\GoodsCategory */

$this->title = Yii::t('goods_category', 'Update Goods Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('goods_category', 'Goods Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-category-update box box-warning">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-pencil"></i>
        <h3 class="box-title"><?= Yii::t('common', 'message_update') ?></h3>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
        'treeOptions' => $treeOptions
    ]) ?>
</div>
