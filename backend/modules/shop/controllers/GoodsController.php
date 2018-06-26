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
     * Lists all Goods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Goods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $model = new Goods();
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if ($model->validate()) {
                //处理主图
                $imgOthersArr = explode(',', $model->img_others);
                $imgOrg = $imgOthersArr[0];//主图
                $imgBaseName = basename($imgOrg);//名称
                $imgPath = Yii::$app->params['goodsMasterPath'];
                FileHelper::createDirectory(Yii::getAlias('@webroot') . $imgPath);
                $img = $imgPath . $imgBaseName;
                Image::thumbnail(Yii::getAlias('@webroot') . $imgOrg, 320, 320)->save(Yii::getAlias('@webroot') . $img);//压缩后重新存储
                $model->img = $img;
                $res = $model->save(false);
                if ($res) {
                    //获取列表页url，方便跳转
                    $url = $this->getReferrerUrl('goods-create');
                    return $this->redirectSuccess($url, Yii::t('common', 'Create Success'));
                }
            }
            //由于图片是服务端验证，所以如果失败就会重新加载前端，这里确保已输入的参数还会显示
            $postData = Yii::$app->request->post();
            $paramNameArr = $postData['paramName'];
            $paramValueArr = $postData['paramValue'];
            $paramSortArr = $postData['paramSort'];
            $paramData = [];
            if (!empty($paramNameArr)) {
                foreach ($paramNameArr as $key => $name) {
                    if (!empty($paramValueArr[$key])) {
                        $paramData[] = [
                            'name' => $name,
                            'value' => $paramValueArr[$key],
                            'sort' => intval($paramSortArr[$key]),
                        ];
                    }
                }
            }
            $model->goods_param = $paramData;
        }
        //为了更新完成后返回列表检索页数原有状态，所以这里先纪录下来
        $this->rememberReferrerUrl('goods-create');

        $model->loadDefaultValues();
        //将整数的金额转为小数显示
        $priceArr = ['price', 'market_price', 'cost_price', 'freight_price'];
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
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //获取列表页url，方便跳转
            $url = $this->getReferrerUrl('goods-update');
            return $this->redirectSuccess($url, Yii::t('common', 'Update Success'));
        } else {
            //为了更新完成后返回列表检索页数原有状态，所以这里先纪录下来
            $this->rememberReferrerUrl('goods-update');
            //将整数的金额转为小数显示
            $priceArr = ['price', 'market_price', 'cost_price', 'freight_price'];
            foreach ($priceArr as $value) {
                $model->$value = Yii::$app->formatter->asDecimal($model->$value / 100, 2);
            }
            return $this->render('update', [
                'model' => $model,
            ]);
        }
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
