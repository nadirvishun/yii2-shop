<?php

namespace backend\modules\shop\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

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
    const GOODS_PROPERTY_YES = 1;//商品属性：是否热销等状态：是
    const GOODS_PROPERTY_NO = 0;//商品属性：是否热销等状态：否

    const FREIGHT_TYPE_TEMPLATE = 0;//运费模板
    const FREIGHT_TYPE_COMMON = 1;//统一运费

    const GOODS_OFFLINE = 0;//下架
    const GOODS_ONLINE = 1;//上架
    const GOODS_RECYCLE = 2;//删除

    const STOCK_TYPE_ORDER = 1;//拍下减库存
    const STOCK_TYPE_PAY = 2;//付款减库存

    const STOCK_ALARM_YES = 1;//库存预警
    const STOCK_ALARM_NO = 2;//库存正常

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
     * 事务
     * @return array
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * 关联
     * @return \yii\db\ActiveQuery
     */
    public function getGoodsParams()
    {
        return $this->hasMany(GoodsParam::className(), ['goods_id' => 'id'])
            ->orderBy(['sort' => SORT_DESC, 'id' => SORT_DESC]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'brand_id', 'sales', 'real_sales', 'click', 'collect',
                'stock', 'stock_alarm', 'stock_type', 'is_freight_free', 'freight_type',
                'freight_id', 'is_new', 'is_hot', 'is_recommend', 'is_limit', 'max_buy',
                'min_buy', 'user_max_buy', 'give_integral', 'sort', 'status',
                'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['category_id', 'title', 'price', 'market_price', 'stock'], 'required'],
            [['img_others', 'content'], 'string'],
            //由于text严格模式下无法设置默认值，这里手动赋值
            [['img_others', 'content'], 'default', 'value' => ''],
            //todo,待确定
            [['freight_id', 'brand_id'], 'default', 'value' => '0'],
            //当选择运费模板，需要运费模板ID不为空
            ['freight_id', 'required', 'when' => function ($model) {
                return $model->freight_type == 0;
            }, 'whenClient' => "function (attribute, value) {
                return $('input[name=\"Goods[freight_type]\"]:checked').val()==0?true:false;
            }"],
            ['img_others', 'validateImgOthers', 'skipOnEmpty' => false],
            [['weight'], 'number'],
            [['goods_sn', 'goods_barcode'], 'string', 'max' => 100],
            [['title', 'sub_title', 'img'], 'string', 'max' => 255],
            [['unit'], 'string', 'max' => 10],
            [['unit'], 'default', 'value' => '件'],
            [['goods_sn'], 'unique'],
            [['price', 'market_price', 'cost_price', 'freight_price'], 'number', 'min' => 0],
            ['market_price', 'compare', 'compareAttribute' => 'price', 'type' => 'number', 'operator' => '>='],//市场价大于等于标价
            [['goods_barcode'], 'unique'],
            //设置默认值为null，数据库中才能唯一索引但是多个null值
            [['goods_barcode'], 'default', 'value' => null],
        ];
    }

    /**
     * 由于img_others是file类型，而这里用ajax上传，走的隐藏字段，所以如果设置required会出错，只能在服务端来判定
     */
    public function validateImgOthers()
    {
        if (empty($this->img_others)) {
            $this->addError('img_others', Yii::t('goods', 'Image can not empty!'));
        }
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

    /**
     *  获取运费方式下拉菜单列表或者某一名称
     * @param bool $key
     * @return array|mixed
     */
    public static function getFreightTypeOptions($key = false)
    {
        $arr = [
            self::FREIGHT_TYPE_TEMPLATE => Yii::t('goods', 'freight template'),
            self::FREIGHT_TYPE_COMMON => Yii::t('goods', 'freight common')
        ];
        return $key === false ? $arr : ArrayHelper::getValue($arr, $key, Yii::t('common', 'Unknown'));
    }

    /**
     * 获取各商品属性下拉菜单
     * @param string $type
     * @param bool $key
     * @return array|mixed
     */
    public static function getGoodsPropertyOptions($type = 'is_new', $key = false)
    {
        if (!in_array($type, ['is_new', 'is_hot', 'is_recommend'])) {
            $arr = [];
        } else {
            $arr = [
                self::GOODS_PROPERTY_NO => Yii::t('goods', 'no'),
                self::GOODS_PROPERTY_YES => Yii::t('goods', 'yes')
            ];
        }
        return $key === false ? $arr : ArrayHelper::getValue($arr, $key, Yii::t('common', 'Unknown'));
    }

    /**
     * 库存预警和库存正常分类
     * @param bool $key
     * @return array|mixed
     */
    public static function getStockAlarmOptions($key = false)
    {
        $arr = [
            self::STOCK_ALARM_NO => Yii::t('goods', 'stock normal'),
            self::STOCK_ALARM_YES => Yii::t('goods', 'stock alarm')
        ];
        return $key === false ? $arr : ArrayHelper::getValue($arr, $key, Yii::t('common', 'Unknown'));
    }

    /**
     *  获取状态下拉菜单列表或者某一名称
     * @param bool $key
     * @param bool $format 是否组装成前台switchInput需要的格式
     * @return array|mixed
     */
    public static function getStockTypeOptions($key = false, $format = false)
    {
        $arr = [
            self::STOCK_TYPE_ORDER => Yii::t('goods', 'pay reduce'),
            self::STOCK_TYPE_PAY => Yii::t('goods', 'order reduce'),
        ];
        if ($key !== false) {
            return ArrayHelper::getValue($arr, $key, Yii::t('common', 'Unknown'));
        } else {
            if ($format === false) {
                return $arr;
            } else {
                $formatArr = [];
                foreach ($arr as $key => $value) {
                    $formatArr[] = [
                        'label' => $value,
                        'value' => $key
                    ];
                }
                return $formatArr;
            }
        }
    }

    /**
     *  获取状态下拉菜单列表或者某一名称
     * @param bool $key
     * @param bool $format 是否组装成前台switchInput需要的格式
     * @return array|mixed
     */
    public static function getStatusOptions($key = false, $format = false)
    {
        $arr = [
            self::GOODS_OFFLINE => Yii::t('goods', 'offline'),
            self::GOODS_ONLINE => Yii::t('goods', 'online'),
            self::GOODS_RECYCLE => Yii::t('goods', 'recycle')
        ];
        if ($key !== false) {
            return ArrayHelper::getValue($arr, $key, Yii::t('common', 'Unknown'));
        } else {
            if ($format === false) {
                return $arr;
            } else {
                $formatArr = [];
                foreach ($arr as $key => $value) {
                    $formatArr[] = [
                        'label' => $value,
                        'value' => $key
                    ];
                }
                return $formatArr;
            }
        }
    }

    /**
     * 获取需要转换为分的字段
     * @return array
     */
    public static function getPriceFields()
    {
        return ['price', 'market_price', 'cost_price', 'freight_price'];
    }

    /**
     * 获取批量操作的类别
     * @param string $type 是取name还是取value
     * @param bool $key
     * @return array|mixed
     */
    public static function getBatchOperations($type = 'name', $key = false)
    {
        if (!in_array($type, ['id', 'name', 'value'])) {
            $arr = [];
        } else {
            $arr = [
                'new' => [
                    'id' => 'is_new',
                    'name' => Yii::t('goods', 'Is New'),
                    'value' => self::GOODS_PROPERTY_YES,
                ],
                'not_new' => [
                    'id' => 'is_new',
                    'name' => Yii::t('goods', 'Not New'),
                    'value' => self::GOODS_PROPERTY_NO,
                ],
                'hot' => [
                    'id' => 'is_hot',
                    'name' => Yii::t('goods', 'Is Hot'),
                    'value' => self::GOODS_PROPERTY_YES,
                ],
                'not_hot' => [
                    'id' => 'is_hot',
                    'name' => Yii::t('goods', 'Not Hot'),
                    'value' => self::GOODS_PROPERTY_NO,
                ],
                'recommend' => [
                    'id' => 'is_recommend',
                    'name' => Yii::t('goods', 'Is Recommend'),
                    'value' => self::GOODS_PROPERTY_YES,
                ],
                'not_recommend' => [
                    'id' => 'is_recommend',
                    'name' => Yii::t('goods', 'Not Recommend'),
                    'value' => self::GOODS_PROPERTY_NO,
                ],
                'status_offline' => [
                    'id' => 'status',
                    'name' => Yii::t('goods', 'offline'),
                    'value' => self::GOODS_OFFLINE
                ],
                'status_online' => [
                    'id' => 'status',
                    'name' => Yii::t('goods', 'online'),
                    'value' => self::GOODS_ONLINE
                ],
                'status_recycle' => [
                    'id' => 'status',
                    'name' => Yii::t('goods', 'recycle'),
                    'value' => self::GOODS_RECYCLE
                ]
            ];
        }
        $subArr = [];
        foreach ($arr as $k => $value) {
            $subArr[$k] = $value[$type];
        }
        return $key === false ? $subArr : ArrayHelper::getValue($subArr, $key, Yii::t('common', 'Unknown'));
    }

    /**
     * 自动生成goods_sn
     * 暂时用yii2自带的生成随机数的方法，后期需要优化
     * @param int $length
     * @return string
     * @throws \yii\base\Exception
     */
    public function generateGoodsSn($length = 32)
    {
        return Yii::$app->security->generateRandomString($length);
    }

    /**
     * 存储前的动作
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        //如果是新增，则自动产生
        if ($this->isNewRecord) {
            if (empty($this->goods_sn)) {
                $this->goods_sn = $this->generateGoodsSn();
            }
        }
        return parent::beforeSave($insert);
    }

    /**
     * 存储后的动作
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\db\Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        //增加商品参数数据
        $postData = Yii::$app->request->post();
        $paramNameArr = isset($postData['paramName']) ? $postData['paramName'] : [];
        $paramValueArr = isset($postData['paramValue']) ? $postData['paramValue'] : [];
        $paramSortArr = isset($postData['paramSort']) ? $postData['paramSort'] : [];
        $goodsParam = new GoodsParam();
        $goodsParam->saveBatchGoodsParam($this->id, $paramNameArr, $paramValueArr, $paramSortArr, $insert);
    }
}
