<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = <?= $generator->generateString('View ' . Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view box box-primary">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-eye"></i>
        <h3 class="box-title"><?="<?="?> Yii::t('common', 'message_view') ?></h3>
    </div>
    <div class="box-body">
        <?= "<?= " ?>DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table table-striped table-bordered detail-view', 'style' => 'word-break:break-all; word-wrap:break-all'],
            'attributes' => [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        echo "                '" . $name . "',\n";
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        echo "                '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
    }
}
?>
            ],
        ]) ?>
        <p style="margin-top:10px">
            <?= "<?= " ?>Html::a(Yii::t('common', 'update'), ['update', <?= $urlParams ?>], ['class' => 'btn btn-warning']) ?>
            <?= "<?= " ?>Html::a(Yii::t('common', 'delete'), ['delete', <?= $urlParams ?>],
                ['class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
        </p>
    </div>
</div>
