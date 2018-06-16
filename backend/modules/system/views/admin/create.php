<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\Admin */
/* @var $act backend\modules\system\controllers\AdminController */
/* @var $avatarUrl backend\modules\system\controllers\AdminController */

$this->title = Yii::t('admin', 'Create Admin');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Admins'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-create box box-success">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-plus"></i>
        <h3 class="box-title"><?= Yii::t('common', 'message_create') ?></h3>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
        'act' => $act,
        'avatarUrl'=>$avatarUrl,
    ]) ?>
</div>
