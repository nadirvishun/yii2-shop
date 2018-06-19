<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\GoodsCategory */

$this->title = Yii::t('goods_category', 'View Goods Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('goods_category', 'Goods Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-category-view box box-primary">
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
                'tree',
                'name',
                'img',
                'lft',
                'rgt',
                'depth',
                'adv_img',
                'adv_type',
                'adv_value',
                'sort',
                'is_recommend',
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
