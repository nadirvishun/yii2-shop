<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin([
        'id' => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form',
        'options' => ['class' => 'box-body']
    ]); ?>

<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
    }
} ?>
    <div class="form-group">
        <?= "<?= " ?>Html::submitButton($model->isNewRecord ? Yii::t('common','create') : Yii::t('common','update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-warning']) ?>

    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
<?= "<?php " ?>
//增加必填字段红星提示
$js = <<<eof
    $(".required").each(function(){
        var label=$(this).children(':first');
        label.html(label.html()+'<i style="color:red">*</i>');
    })
eof;
$this->registerJs($js);
?>
