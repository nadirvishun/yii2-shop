<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\Setting */
/* @var $treeOptions backend\modules\system\controllers\SettingController */
/* @var $placeholderOptions backend\modules\system\controllers\SettingController */
$this->title = Yii::t('setting', 'Create Setting');
$this->params['breadcrumbs'][] = ['label' => Yii::t('setting', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="backend-setting-create box box-success">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-plus"></i>
        <h3 class="box-title"><?= Yii::t('common', 'message_create') ?></h3>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
        'placeholderOptions' => $placeholderOptions
    ]) ?>
</div>
