<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\Goods */

$this->title = Yii::t('goods', 'View Goods');
$this->params['breadcrumbs'][] = ['label' => Yii::t('goods', 'Goods'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-view box box-primary">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-eye"></i>
        <h3 class="box-title"><?= Yii::t('common', 'message_view') ?></h3>
    </div>
    <div class="box-body">
        <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table table-striped table-bordered detail-view', 'style' => 'word-break:break-all; word-wrap:break-all'],
            'attributes' => [
                'id',
                'goods_sn',
                'goods_barcode',
                'title',
                'sub_title',
                'category_id',
                'brand_id',
                'price',
                'unit',
                'market_price',
                'cost_price',
                'img',
                'img_others:ntext',
                'content:ntext',
                'sales',
                'real_sales',
                'click',
                'collect',
                'stock',
                'stock_alarm',
                'stock_type',
                'weight',
                'is_freight_free',
                'freight_type',
                'freight_id',
                'freight_price',
                'is_new',
                'is_hot',
                'is_recommend',
                'is_limit',
                'max_buy',
                'min_buy',
                'user_max_buy',
                'give_integral',
                'sort',
                'status',
                'created_by',
                'created_at',
                'updated_by',
                'updated_at',
            ],
        ]) ?>
        <p style="margin-top:10px">
            <?= Html::a(Yii::t('common', 'update'), ['update', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
            <?= Html::a(Yii::t('common', 'delete'), ['delete', 'id' => $model->id],
                ['class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
        </p>
    </div>
</div>
