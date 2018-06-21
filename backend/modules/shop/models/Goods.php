<?php

namespace backend\modules\shop\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%goods}}".
 *
 * @property integer $id
 * @property string $goods_sn
 * @property string $goods_barcode
 * @property string $title
 * @property string $sub_title
 * @property integer $category_id
 * @property integer $brand_id
 * @property integer $price
 * @property string $unit
 * @property integer $market_price
 * @property integer $cost_price
 * @property string $img
 * @property string $img_others
 * @property string $content
 * @property integer $sales
 * @property integer $real_sales
 * @property integer $click
 * @property integer $collect
 * @property integer $stock
 * @property integer $stock_alarm
 * @property integer $stock_type
 * @property string $weight
 * @property integer $is_freight_free
 * @property integer $freight_type
 * @property integer $freight_id
 * @property integer $freight_price
 * @property integer $is_new
 * @property integer $is_hot
 * @property integer $is_recommend
 * @property integer $is_limit
 * @property integer $max_buy
 * @property integer $min_buy
 * @property integer $user_max_buy
 * @property integer $give_integral
 * @property integer $sort
 * @property integer $status
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods}}';
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
            [['category_id', 'brand_id', 'sales',
                'real_sales', 'click', 'collect', 'stock', 'stock_alarm', 'stock_type',
                'is_freight_free', 'freight_type', 'freight_id', 'freight_price', 'is_new',
                'is_hot', 'is_recommend', 'is_limit', 'max_buy', 'min_buy', 'user_max_buy',
                'give_integral', 'sort', 'status', 'created_by', 'created_at', 'updated_by',
                'updated_at'], 'integer'],
            [['category_id', 'title', 'price', 'market_price', 'stock', 'img'], 'required'],
            [['img_others', 'content'], 'string'],
            //由于text严格模式下无法设置默认值，这里手动赋值
            [['img_others,content'], 'default', 'value' => ''],
            [['weight'], 'number'],
            [['goods_sn', 'goods_barcode'], 'string', 'max' => 100],
            [['title', 'sub_title', 'img'], 'string', 'max' => 255],
            [['unit'], 'string', 'max' => 10],
            [['goods_sn'], 'unique'],//todo,自动生成
            [['price', 'market_price', 'cost_price'], 'number', 'min' => 0],
            [['price', 'market_price', 'cost_price'], 'filter', 'filter' => function ($value) {
                return $value * 100;
            }],
            [['goods_barcode'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('goods', 'ID'),
            'goods_sn' => Yii::t('goods', 'Goods Sn'),
            'goods_barcode' => Yii::t('goods', 'Goods Barcode'),
            'title' => Yii::t('goods', 'Title'),
            'sub_title' => Yii::t('goods', 'Sub Title'),
            'category_id' => Yii::t('goods', 'Category ID'),
            'brand_id' => Yii::t('goods', 'Brand ID'),
            'price' => Yii::t('goods', 'Price'),
            'unit' => Yii::t('goods', 'Unit'),
            'market_price' => Yii::t('goods', 'Market Price'),
            'cost_price' => Yii::t('goods', 'Cost Price'),
            'img' => Yii::t('goods', 'Img'),
            'img_others' => Yii::t('goods', 'Img Others'),
            'content' => Yii::t('goods', 'Content'),
            'sales' => Yii::t('goods', 'Sales'),
            'real_sales' => Yii::t('goods', 'Real Sales'),
            'click' => Yii::t('goods', 'Click'),
            'collect' => Yii::t('goods', 'Collect'),
            'stock' => Yii::t('goods', 'Stock'),
            'stock_alarm' => Yii::t('goods', 'Stock Alarm'),
            'stock_type' => Yii::t('goods', 'Stock Type'),
            'weight' => Yii::t('goods', 'Weight'),
            'is_freight_free' => Yii::t('goods', 'Is Freight Free'),
            'freight_type' => Yii::t('goods', 'Freight Type'),
            'freight_id' => Yii::t('goods', 'Freight ID'),
            'freight_price' => Yii::t('goods', 'Freight Price'),
            'is_new' => Yii::t('goods', 'Is New'),
            'is_hot' => Yii::t('goods', 'Is Hot'),
            'is_recommend' => Yii::t('goods', 'Is Recommend'),
            'is_limit' => Yii::t('goods', 'Is Limit'),
            'max_buy' => Yii::t('goods', 'Max Buy'),
            'min_buy' => Yii::t('goods', 'Min Buy'),
            'user_max_buy' => Yii::t('goods', 'User Max Buy'),
            'give_integral' => Yii::t('goods', 'Give Integral'),
            'sort' => Yii::t('goods', 'Sort'),
            'status' => Yii::t('goods', 'Status'),
            'created_by' => Yii::t('goods', 'Created By'),
            'created_at' => Yii::t('goods', 'Created At'),
            'updated_by' => Yii::t('goods', 'Updated By'),
            'updated_at' => Yii::t('goods', 'Updated At'),
        ];
    }
}
