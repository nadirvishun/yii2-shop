<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\GoodsBrand */

$this->title = Yii::t('goods_brand', 'Create Goods Brand');
$this->params['breadcrumbs'][] = ['label' => Yii::t('goods_brand', 'Goods Brands'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-brand-create box box-success">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-plus"></i>
        <h3 class="box-title"><?= Yii::t('common', 'message_create') ?></h3>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
