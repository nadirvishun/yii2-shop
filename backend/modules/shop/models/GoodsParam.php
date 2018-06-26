<?php

namespace backend\modules\shop\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%goods_param}}".
 *
 * @property integer $id
 * @property integer $goods_id
 * @property string $name
 * @property string $value
 * @property integer $sort
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 */
class GoodsParam extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_param}}';
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
            [['goods_id', 'sort', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['name', 'value'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('goods_param', 'ID'),
            'goods_id' => Yii::t('goods_param', 'Goods ID'),
            'name' => Yii::t('goods_param', 'Name'),
            'value' => Yii::t('goods_param', 'Value'),
            'sort' => Yii::t('goods_param', 'Sort'),
            'created_by' => Yii::t('goods_param', 'Created By'),
            'created_at' => Yii::t('goods_param', 'Created At'),
            'updated_by' => Yii::t('goods_param', 'Updated By'),
            'updated_at' => Yii::t('goods_param', 'Updated At'),
        ];
    }

    /**
     * 保存参数
     * @param $goodsId
     * @param $paramNameArr
     * @param $paramValueArr
     * @param $paramSortArr
     * @param $insert
     */
    public function saveBatchGoodsParam($goodsId, $paramNameArr, $paramValueArr, $paramSortArr, $insert)
    {
        if (!$insert) {
            //todo,删除原有的
        }
        $insertData = [];
        if (!empty($paramNameArr)) {
            foreach ($paramNameArr as $key => $name) {
                if (!empty($paramValueArr[$key])) {
                    $insertData[] = [
                        $goodsId,
                        $name,
                        $paramValueArr[$key],
                        intval($paramSortArr[$key]),
                        time(),
                        Yii::$app->user->id,
                        time(),
                        Yii::$app->user->id,
                    ];
                }
            }
        }
        if (!empty($insertData)) {
            Yii::$app->db->createCommand()->batchInsert(GoodsParam::tableName(), [
                'goods_id',
                'name',
                'value',
                'sort',
                'created_by',
                'created_at',
                'updated_by',
                'updated_at'
            ], $insertData);
        }
    }
}
