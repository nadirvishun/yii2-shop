<?php

namespace backend\modules\shop\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%goods_sku}}".
 *
 * @property integer $id
 * @property integer $goods_id
 * @property string $goods_spec
 * @property string $goods_sn
 * @property string $goods_barcode
 * @property integer $price
 * @property integer $market_price
 * @property integer $cost_price
 * @property integer $stock
 * @property integer $stock_alarm
 * @property string $weight
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 */
class GoodsSku extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_sku}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id', 'price', 'market_price', 'cost_price', 'stock', 'stock_alarm', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['goods_spec'], 'string'],
            [['weight'], 'number'],
            [['goods_sn', 'goods_barcode'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('goods_sku', 'ID'),
            'goods_id' => Yii::t('goods_sku', 'Goods ID'),
            'goods_spec' => Yii::t('goods_sku', 'Goods Spec'),
            'goods_sn' => Yii::t('goods_sku', 'Goods Sn'),
            'goods_barcode' => Yii::t('goods_sku', 'Goods Barcode'),
            'price' => Yii::t('goods_sku', 'Price'),
            'market_price' => Yii::t('goods_sku', 'Market Price'),
            'cost_price' => Yii::t('goods_sku', 'Cost Price'),
            'stock' => Yii::t('goods_sku', 'Stock'),
            'stock_alarm' => Yii::t('goods_sku', 'Stock Alarm'),
            'weight' => Yii::t('goods_sku', 'Weight'),
            'created_by' => Yii::t('goods_sku', 'Created By'),
            'created_at' => Yii::t('goods_sku', 'Created At'),
            'updated_by' => Yii::t('goods_sku', 'Updated By'),
            'updated_at' => Yii::t('goods_sku', 'Updated At'),
        ];
    }

    /**
     * 新增或修改SKU
     * @param $skuArr
     * @param $goodsId
     */
    public function saveSku($skuArr, $goodsId)
    {
        if (!empty($skuArr)) {
            //去掉日志记录
            \yii\base\Event::off(\yii\db\BaseActiveRecord::className(), \yii\db\BaseActiveRecord::EVENT_AFTER_UPDATE,
                ['backend\modules\system\models\AdminLog', 'eventUpdate']);
            \yii\base\Event::off(\yii\db\BaseActiveRecord::className(), \yii\db\BaseActiveRecord::EVENT_AFTER_INSERT,
                ['backend\modules\system\models\AdminLog', 'eventInsert']);
            //记录现有的sku_id;
            $skuIdArr = [];
            foreach ($skuArr as $sku) {
                $goodsSpec = json_encode($sku['value'],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $data = [
                    'goods_id' => $goodsId,
                    'goods_spec' => $goodsSpec,
                    'goods_sn' => $sku['goods_sn'],
                    'goods_barcode' => $sku['goods_barcode'],
                    'price' => intval($sku['price'] * 100),
                    'market_price' => intval($sku['market_price'] * 100),
                    'cost_price' => intval($sku['cost_price'] * 100),
                    'stock' => $sku['stock'],
                    'stock_alarm' => $sku['stock_alarm'],
                    'weight' => $sku['stock_alarm'],
                ];
                $skuId = $sku['sku_id'];
                //如果没有，则需要写入
                if (empty($skuId)) {
                    $model = new self();
                    $model->load($data, '');
                    $model->save();
                    $skuIdArr[] = $model->id;
                } else {
                    //如果有，则需要修改
                    $model = static::findOne($skuId);
                    $model->load($data, '');
                    $model->save();
                    $skuIdArr[] = $skuId;
                }
            }
            //清理没有用的SKU数据
            static::deleteAll(['and', 'goods_id=:goods_id', ['not in', 'id', $skuIdArr]], [':goods_id' => $goodsId]);
        }
    }

    /**
     * 删除SKU
     * @param $goodsId
     */
    public function deleteSku($goodsId)
    {
        static::deleteAll(['goods_id' => $goodsId]);
    }
}
