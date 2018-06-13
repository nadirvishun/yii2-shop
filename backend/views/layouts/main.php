<?php
use kartik\dialog\Dialog;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
dmstr\web\AdminLteAsset::register($this);
backend\assets\AppAsset::register($this);

$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
?>
<!--引入弹出框小部件-->
<?= Dialog::widget([
    'libName' => 'krajeeDialog',
    'options' => [
        'draggable' => true,
    ]
]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<!--下面也没什么必要了，改用js来控制样式-->
<!--<body class="hold-transition --><? //= \dmstr\helpers\AdminLteHelper::skinClass() ?><!-- sidebar-mini">-->
<?php $this->beginBody() ?>
<div class="wrapper">

    <?= $this->render(
        'header.php',
        ['directoryAsset' => $directoryAsset]
    ) ?>

    <?= $this->render(
        'left.php',
        ['directoryAsset' => $directoryAsset]
    )
    ?>

    <?= $this->render(
        'content.php',
        ['content' => $content, 'directoryAsset' => $directoryAsset]
    ) ?>

</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

