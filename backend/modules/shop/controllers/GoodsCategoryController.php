<?php

namespace backend\modules\shop\controllers;

use Yii;
use backend\modules\shop\models\GoodsCategory;
use yii\caching\TagDependency;
use yii\data\ActiveDataProvider;
use backend\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * GoodsCategoryController implements the CRUD actions for GoodsCategory model.
 */
class GoodsCategoryController extends BaseController
{
    /**
     * Lists all GoodsCategory models.
     * @return mixed
     */
    public function actionIndex($id = null)
    {
        if (Yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');//获取ID
            $model = GoodsCategory::findOne($id);
            $attribute = Yii::$app->request->post('editableAttribute');//获取名称
            $output = '';
            $message = '';
            if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
                $output = $model->$attribute;
            } else {
                //由于本插件不会自动捕捉model的error，所以需要放在$message中展示出来
                $message = $model->getFirstError($attribute);
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['output' => $output, 'message' => $message];
        } else {
            //todo,目前此widget不支持sort，后续可能改进，如果后期菜单太多，可改为懒加载
            $dataProvider = new ActiveDataProvider([
                'query' => GoodsCategory::find()->orderBy(['sort' => SORT_DESC, 'id' => SORT_ASC]),
//            'sort' => ['defaultOrder' => ['sort' => SORT_ASC, 'id' => SORT_ASC]]
            ]);
            //初始只能制定单个的node，而不能选定某一level来初始显示。todo，后续插件优化
            $initial = GoodsCategory::findOne(2);
            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'initial' => $initial,
            ]);
        }
    }

    /**
     * Displays a single GoodsCategory model.
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
     * Creates a new GoodsCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
//        print_r(GoodsCategory::asJsTree());
        $model = new GoodsCategory();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            //如果pid为0，则创建顶级目录，否则归属到pid子类中
            if (empty($model->pid)) {
                $model->makeRoot();
            } else {
                $parentModel = GoodsCategory::findOne(['id' => $model->pid]);
                $model->prependTo($parentModel);
            }
            //获取列表页url，方便跳转
            $url = $this->getReferrerUrl('goods-category-create');
            return $this->redirectSuccess($url, Yii::t('common', 'Create Success'));
        }
        //为了更新完成后返回列表检索页数原有状态，所以这里先纪录下来
        $this->rememberReferrerUrl('goods-category-create');

        $model->loadDefaultValues();
        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing GoodsCategory model.
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
            $url = $this->getReferrerUrl('goods-category-update');
            return $this->redirectSuccess($url, Yii::t('common', 'Update Success'));
        } else {
            //为了更新完成后返回列表检索页数原有状态，所以这里先纪录下来
            $this->rememberReferrerUrl('goods-category-update');

            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing GoodsCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        $url = Yii::$app->request->referrer;
        //如果是从view中删除，则返回列表页
        if (strpos(urldecode($url), 'goods-category/view') !== false) {
            $url = ['index'];
        }
        return $this->redirectSuccess($url, Yii::t('common', 'Delete Success'));
    }

    /**
     * Finds the GoodsCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GoodsCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GoodsCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
        }
    }
}
