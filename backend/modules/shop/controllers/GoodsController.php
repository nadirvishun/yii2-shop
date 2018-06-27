<?php

namespace backend\modules\shop\controllers;

use backend\modules\shop\models\GoodsParam;
use Yii;
use backend\modules\shop\models\Goods;
use backend\modules\shop\models\search\GoodsSearch;
use backend\controllers\BaseController;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * GoodsController implements the CRUD actions for Goods model.
 */
class GoodsController extends BaseController
{
    /**
     * 上传相关
     * @return array
     */
    public function actions()
    {
        return [
            //ueditor上传
            'ueditorUpload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => Yii::$app->params['ueditorConfig']
            ],
            //fileInput上传
            'upload' => [
                'class' => 'common\components\UploadAction',
                'path' => Yii::$app->params['goodsPath'],//上传路径
                'rule' => [
                    'skipOnEmpty' => false,
                    'extensions' => 'jpg,jpeg,png',
                    'maxSize' => 1024000,
                ]
            ]
        ];
    }

    /**
     * 上架中的商品
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GoodsSearch();
        $status = Goods::GOODS_ONLINE;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $status);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 下架商品
     */
    public function actionOffline()
    {
        if (Yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');//获取ID
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = Goods::findOne($id);
            $output = '';
            $message = '';
            //由于传递的数据是二维数组，将其转为一维
            $attribute = Yii::$app->request->post('editableAttribute');//获取名称
            $posted = current(Yii::$app->request->post('Goods'));
            $post = ['Goods' => $posted];
            if ($model->load($post) && $model->validate()) {
                $priceArr = Goods::getPriceFields();//获取以分为单位存储的价格字段数组
                $priceError = false;//价格验证错误，默认没有
                if (in_array($attribute, $priceArr)) {
                    $model->$attribute = intval($model->$attribute * 100);
                    //判定商品价格必须小于市场价格，由于市场价格不传递，导致变为分后rule不正确，所以在这里判定
                    if ($attribute == 'price') {
                        if ($model->$attribute > $model->market_price) {
                            $model->addError($attribute, Yii::t('goods', 'price must be less than or equal to market price'));
                            $priceError = true;
                        }
                    }
                }
                if (!$priceError && $model->save(false)) {
                    if (in_array($attribute, $priceArr)) {
                        //价格格式化
                        $output = Yii::$app->formatter->asDecimal($model->$attribute / 100, 2);
                    } elseif ($attribute == 'stock') {
                        //库存显示预警
                        if ($model->stock==0 ||($model->stock_alarm !== 0 && $model->stock_alarm >= $model->$attribute)) {
                            $output = '<span style="color:red">' . $model->$attribute . '</span>';
                        } else {
                            $output = $model->$attribute;
                        }
                    } else {
                        $output = $model->$attribute;
                    }
                    return ['output' => $output, 'message' => $message];
                }
            }
            //由于本插件不会自动捕捉model的error，所以需要放在$message中展示出来
            $message = $model->getFirstError($attribute);
            return ['output' => $output, 'message' => $message];
        } else {
            $searchModel = new GoodsSearch();
            $status = Goods::GOODS_OFFLINE;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $status);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * 回收站商品
     */
    public function actionRecycle()
    {
        $searchModel = new GoodsSearch();
        $status = Goods::GOODS_RECYCLE;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $status);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Displays a single Goods model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    /*public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }*/

    /**
     * Creates a new Goods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $model = new Goods();
        $priceArr = Goods::getPriceFields();//获取以分为单位存储的价格字段数组
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                //处理主图
                $imgOthersArr = explode(',', $model->img_others);
                $imgOrg = $imgOthersArr[0];//主图
                $imgBaseName = basename($imgOrg);//名称
                $imgPath = Yii::$app->params['goodsMasterPath'];
                FileHelper::createDirectory(Yii::getAlias('@webroot') . $imgPath);
                $img = $imgPath . $imgBaseName;
                Image::thumbnail(Yii::getAlias('@webroot') . $imgOrg, 320, 320)->save(Yii::getAlias('@webroot') . $img);//压缩后重新存储
                $model->img = $img;
                //处理价格为分
                foreach ($priceArr as $value) {
                    $model->$value = intval($model->$value * 100);
                }
                $res = $model->save(false);
                if ($res) {
                    //获取列表页url，方便跳转
                    $url = $this->getReferrerUrl('goods-create');
                    return $this->redirectSuccess($url, Yii::t('common', 'Create Success'));
                }
            }
        }
        //为了更新完成后返回列表检索页数原有状态，所以这里先纪录下来
        $this->rememberReferrerUrl('goods-create');

        $model->loadDefaultValues();
        //将整数的金额转为小数显示
        foreach ($priceArr as $value) {
            $model->$value = Yii::$app->formatter->asDecimal($model->$value / 100, 2);
        }
        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing Goods model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $priceArr = Goods::getPriceFields();//获取以分为单位存储的价格字段数组
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                //判定主图是否有变动
                $imgOthersArr = explode(',', $model->img_others);
                $imgOrg = $imgOthersArr[0];//主图
                $imgBaseName = basename($imgOrg);//名称
                //只有变动才重新存储主图
                if ($imgBaseName != basename($model->img)) {
                    $imgPath = Yii::$app->params['goodsMasterPath'];
                    FileHelper::createDirectory(Yii::getAlias('@webroot') . $imgPath);
                    $img = $imgPath . $imgBaseName;
                    Image::thumbnail(Yii::getAlias('@webroot') . $imgOrg, 320, 320)->save(Yii::getAlias('@webroot') . $img);//压缩后重新存储
                    $model->img = $img;
                }
                //处理价格为分
                foreach ($priceArr as $value) {
                    $model->$value = intval($model->$value * 100);
                }
                $res = $model->save(false);
                if ($res) {
                    //获取列表页url，方便跳转
                    $url = $this->getReferrerUrl('goods-update');
                    return $this->redirectSuccess($url, Yii::t('common', 'Update Success'));
                }
            }
        }
        //为了更新完成后返回列表检索页数原有状态，所以这里先纪录下来
        $this->rememberReferrerUrl('goods-update');
        //将整数的金额转为小数显示
        foreach ($priceArr as $value) {
            $model->$value = Yii::$app->formatter->asDecimal($model->$value / 100, 2);
        }
        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /**
     * Deletes an existing Goods model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        $url = Yii::$app->request->referrer;
        //如果是从view中删除，则返回列表页
        if (strpos(urldecode($url), 'goods/view') !== false) {
            $url = ['index'];
        }
        return $this->redirectSuccess($url, Yii::t('common', 'Delete Success'));
    }

    /**
     * Finds the Goods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Goods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Goods::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
        }
    }

    /**
     * 上传权限与列表权限共用
     * @param $permission
     * @return mixed
     */
    public function getSamePermission($permission)
    {
        $arr = [
            'shop/goods/ueditorUpload' => 'shop/goods/index',
            'shop/goods/upload' => 'shop/goods/index'
        ];
        return isset($arr[$permission]) ? $arr[$permission] : $permission;
    }
}
