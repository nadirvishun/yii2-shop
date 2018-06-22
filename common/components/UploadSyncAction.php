<?php
/**
 * fileInput上传独立控制器
 * 同步上传
 */

namespace common\components;

use Yii;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * ajax上传文件action
 */
class UploadSyncAction extends Action
{
    /**
     * 上传字段名称
     * 可以不设置，在上传时传递name参数也可以，优先使用上传时的name参数
     * @var string
     */
    public $name;
    /**
     * 保存路径
     * @var string
     */
    public $path;
    /**
     * 验证规则
     * ['extensions'=>'png,jpg,gif',...]
     * @var array
     */
    public $rule = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        //关闭csrf
        Yii::$app->request->enableCsrfValidation = false;
        //默认名称
        if (empty($this->name)) {
            $this->name = 'file_data';
        }
        //默认路径
        if (empty($this->path)) {
            $this->path = Yii::$app->params['defaultPath'];
        }
    }

    /**
     * 运行
     * @throws \yii\base\Exception
     */
    public function run()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $action = Yii::$app->request->get('action');
            if ($action == 'upload') {
                //如果是上传
                return $this->upload();
            } elseif ($action == 'delete') {
                //如果是删除
                return $this->delete();
            } else {
                //参数错误
                return ['error' => Yii::t('common', 'Invalid Parameter')];
            }
        }
    }

    /**
     * 上传文件
     * 由于用的是kartik的yii2-widget-fileinput组件，所以需要返回组件需要的格式
     * @throws \yii\base\Exception
     */
    public function upload()
    {
        //文件字段名称
        $name = Yii::$app->request->post('name');
        if (empty($name)) {
            $name = $this->name;
        }
        //上传的文件
        $fileInstances = UploadedFile::getInstancesByName($name);
        //验证文件上传,todo,待验证个数等
//        $model = new DynamicModel([$name => $fileInstances]);
//        $model->addRule($name, 'file', $this->rule)->validate();
//        if ($model->hasErrors()) {
//            $error = $model->getFirstError($name);
//            return ['error'=>$error];
//        }
        $errors = [];
        $saveFiles = [];
        $configs = [];
        $errorKeys = [];
        foreach ($fileInstances as $key => $fileInstance) {
            $error = '';
            $uploadError = '';
            //验证文件上传
            $model = new DynamicModel([$name => $fileInstance]);
            $model->addRule($name, 'file', $this->rule)->validate();
            if ($model->hasErrors()) {
                $error = $model->getFirstError($name);
                $errors[] = $error;
                $errorKeys[] = $key;
            }
            //如果验证通过，则保存
            if (empty($error)) {
                //如果没有目录，则创建目录
                FileHelper::createDirectory(Yii::getAlias('@webroot') . $this->path);
                //保存文件
                $newName = time() . rand(1000, 9999);//文件重命名
                if (!$fileInstance->saveAs(Yii::getAlias('@webroot') . $this->path . $newName . '.' . $fileInstance->extension)) {
                    $uploadError = Yii::t('common', 'Upload failed!');
                    $error[] = $uploadError;
                    $errorKeys[] = $key;
                }
                if (empty($uploadError)) {
                    //返回正确信息
                    $saveFile = $this->path . $newName . '.' . $fileInstance->extension;
                    $saveFiles[] = $saveFile;
                    $configs[] = [
                        'caption' => $newName . '.' . $fileInstance->extension,
                        'url' => Url::to(['upload', 'action' => 'delete']),
                        //todo,后续如果用数据库存储，则需要返回对应的id，方便删除
                        'key' => $saveFile
                    ];
                }
            }
        }
        //todo,返回有问题，如果返回initialPreview等，那直接出错的就自动移除了，如果不返回，那删除等又有问题
//        if (!empty($errors)) {
//            return ['error' => implode(',', $errors), 'errorkeys' => $errorKeys];
//        }

        return [
            'error' => implode(',', $errors),
            'errorkeys' => $errorKeys,
//            'initialPreview' => $saveFiles, //必须返回数据才能调用ajax删除
//            'initialPreviewConfig' => $configs,
        ];
    }

    /**
     * 删除文件
     */
    public function delete()
    {
        $key = Yii::$app->request->post('key');
        @unlink(Yii::getAlias('@webroot') . $key);
        //todo,后续如果用数据库存储，需删除数据，可能返回错误什么的['error'=>'error message']
        return [
            'key' => $key
        ];
    }
}