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
     * 上传
     * @return array
     */
    public function actions()
    {
        return [
            //fileInput上传
            'upload' => [
                'class' => 'common\components\UploadAction',
                'path' => Yii::$app->params['goodsCategoryPath'],//上传路径
                'rule' => [
                    'skipOnEmpty' => false,
                    'extensions' => 'jpg,jpeg,png,gif',
                    'maxSize' => 1024000
                ]
            ]
        ];
    }

    /**
     * Lists all GoodsCategory models.
     * @param null $id
     * @return mixed
     */
    public function actionIndex($id = null)
    {
        if (Yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');//获取ID
            $model = GoodsCategory::findOne($id);
            //获取上级pid以免验证时失败
            $pid = $model->parents(1)->select('id')->scalar();
            $model->pid = $pid;
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
            $dataProvider = new ActiveDataProvider([
                'query' => GoodsCategory::find(),
            ]);
            //初始只能制定单个的node，而不能选定某一level来初始显示。todo，后续插件优化
//            $initial = GoodsCategory::findOne(2);
            return $this->render('index', [
                'dataProvider' => $dataProvider,
//                'initial' => $initial,
            ]);
        }
    }

    /**
     * Creates a new GoodsCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $pid
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate($pid = 1)
    {
        $model = new GoodsCategory();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            //现在统一在一个树内，所以不存在创建顶级树的情况
            $parentModel = $this->findModel($model->pid);
            $model->appendTo($parentModel, false);

            //获取列表页url，方便跳转
            $url = $this->getReferrerUrl('goods-category-create');
            return $this->redirectSuccess($url, Yii::t('common', 'Create Success'));
        }
        //为了更新完成后返回列表检索页数原有状态，所以这里先纪录下来
        $this->rememberReferrerUrl('goods-category-create');

        $model->loadDefaultValues();
        //如果仅仅是建下级，需要传递父级的id
        if (!empty($pid)) {
            //判断pid是否存在
            $this->findModel($pid);
            $model->pid = $pid;
        }
        //获取分类下拉菜单
        $treeOptions = GoodsCategory::getGoodsCategoryTreeOptions();
        return $this->render('create', [
            'model' => $model,
            'treeOptions' => $treeOptions
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
        //获取pid
        $pid = $model->parents(1)->select('id')->scalar();
        $model->scenario = 'update';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            //判定父ID是否有变化，有变化才调用改动
            if ($pid != $model->pid) {
                //现在统一在一个树内，所以不存在创建顶级树的情况
                $parentModel = $this->findModel($model->pid);
                $model->appendTo($parentModel, false);
            } else {
                //如果没有变化，直接保存即可，以免更新顺序发生变化
                $model->save(false);
            }
            //获取列表页url，方便跳转
            $url = $this->getReferrerUrl('goods-category-update');
            return $this->redirectSuccess($url, Yii::t('common', 'Update Success'));
        }
        //为了更新完成后返回列表检索页数原有状态，所以这里先纪录下来
        $this->rememberReferrerUrl('goods-category-update');
        $model->pid = $pid;//上级目录显示
        $treeOptions = GoodsCategory::getGoodsCategoryTreeOptions();
        return $this->render('update', [
            'model' => $model,
            'treeOptions' => $treeOptions
        ]);

    }

    /**
     * Deletes an existing GoodsCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        //判定是否有下级
        $children = $model->children()->one();
        if (!empty($children)) {
            return $this->redirectError(['index'],
                Yii::t('good_category', 'This node has children ,please delete children first'));
        }
        $model->delete();
        return $this->redirectSuccess(['index'], Yii::t('common', 'Delete Success'));
    }

    /**
     * 拖拽改变上下关系及排序
     * @throws NotFoundHttpException
     */
    public function actionMove()
    {
        $id = Yii::$app->request->post('id');
        $target = Yii::$app->request->post('target');
        $position = Yii::$app->request->post('position');
        //当前要移动的
        $model = $this->findModel($id);
        //目标
        $targetModel = $this->findModel($target);
        $code = 0;
        $msg = Yii::t('common', 'Update Success');
        //不同的位置
        switch ($position) {
            case 0://之前
                $model->insertBefore($targetModel, false);
                break;
            case 1://里面
                $model->appendTo($targetModel, false);
                break;
            case 2://之后
                $model->insertAfter($targetModel, false);
                break;
            default:
                $code = 1;
                $msg = Yii::t('common', 'Invalid Parameter');
                break;
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['code' => $code, 'msg' => $msg];
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

    /**
     * 拖拽权限与修改权限共用
     * @param $permission
     * @return mixed
     */
    public function getSamePermission($permission)
    {
        $arr = [
            'shop/goods-category/move' => 'shop/goods-category/update',
            'shop/goods-category/upload' => 'shop/goods-category/index'
        ];
        return isset($arr[$permission]) ? $arr[$permission] : $permission;
    }
}
