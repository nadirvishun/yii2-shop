<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\Admin */
/* @var $act backend\modules\system\controllers\AdminController */
/* @var $avatarUrl backend\modules\system\controllers\AdminController */

$this->title = Yii::t('admin', 'Update Admin');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Admins'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-update box box-warning">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-pencil"></i>
        <h3 class="box-title"><?= Yii::t('common', 'message_update') ?></h3>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
        'act' => $act,
        'avatarUrl'=>$avatarUrl,
    ]) ?>
</div>
