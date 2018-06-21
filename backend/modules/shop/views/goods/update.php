<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\Goods */

$this->title = Yii::t('goods', 'Update Goods');
$this->params['breadcrumbs'][] = ['label' => Yii::t('goods', 'Goods'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-update box box-warning">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-pencil"></i>
        <h3 class="box-title"><?= Yii::t('common', 'message_update') ?></h3>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
