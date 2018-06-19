<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\GoodsCategory */

$this->title = Yii::t('goods_category', 'Create Goods Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('goods_category', 'Goods Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-category-create box box-success">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-plus"></i>
        <h3 class="box-title"><?= Yii::t('common', 'message_create') ?></h3>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
