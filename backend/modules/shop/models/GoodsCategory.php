<?php

namespace backend\modules\shop\models;

use creocoder\nestedsets\NestedSetsBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\caching\DbDependency;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%goods_category}}".
 *
 * @property integer $id
 * @property integer $tree
 * @property string $name
 * @property string $img
 * @property string $lft
 * @property string $rgt
 * @property string $depth
 * @property string $adv_img
 * @property string $adv_type
 * @property string $adv_value
 * @property integer $is_recommend
 * @property integer $status
 * @property string $created_by
 * @property string $created_at
 * @property string $updated_by
 * @property string $updated_at
 */
class GoodsCategory extends \yii\db\ActiveRecord
{
    const STATUS_HIDE = 0;//隐藏
    const STATUS_VISIBLE = 1;//显示

    const RECOMMEND_OFF = 0;//推荐
    const RECOMMEND_ON = 1;//不推荐

    CONST ADV_TYPE_NONE = 0;//广告跳转类型：不跳转
    CONST ADV_TYPE_URL = 1;//广告跳转类型：网址url
    CONST ADV_TYPE_GOODS = 2;//广告跳转类型：商品ID
    CONST ADV_TYPE_ARTICLE = 3;//广告跳转类型：文章ID
    /**
     * @var integer $pid 父ID
     */
    public $pid;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                //设置单一树，否则如果有多个树，树之间的排序无法处理
//                'treeAttribute' => 'tree',
            ],
        ];
    }

    /**
     * 开启事务
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * 查询相关方法
     * @return GoodsCategoryQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new GoodsCategoryQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'tree',
                    'lft',
                    'rgt',
                    'depth',
                    'adv_type',
                    'is_recommend',
                    'status',
                    'created_by',
                    'created_at',
                    'updated_by',
                    'updated_at'
                ],
                'integer'
            ],
            [['name', 'img', 'adv_img', 'adv_value'], 'string', 'max' => 255],
            [['name', 'pid'], 'required'],
            //验证pid是否存在
            ['pid', 'exist', 'targetAttribute' => 'id'],
            //当更新时，父ID不能为自身或其下级节点
            ['pid', 'validatePid', 'on' => 'update'],
        ];
    }

    /**
     * 更新时验证选择的pid不能为本身及其下级节点
     */
    public function validatePid()
    {
        //获取其所有下级
        $countries = static::findOne(['id' => $this->id]);
        $childIds = $countries->children()->select('id')->asArray()->column();
        $childIds = array_merge([$this->id], $childIds);//包含自身
        if (in_array($this->pid, $childIds)) {
            $this->addError('pid', Yii::t('goods_category', 'Parent ID can not be itself or its subordinate node'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('goods_category', 'ID'),
            'pid' => Yii::t('goods_category', 'Pid'),
            'tree' => Yii::t('goods_category', 'Tree'),
            'name' => Yii::t('goods_category', 'Name'),
            'img' => Yii::t('goods_category', 'Img'),
            'lft' => Yii::t('goods_category', 'Lft'),
            'rgt' => Yii::t('goods_category', 'Rgt'),
            'depth' => Yii::t('goods_category', 'Depth'),
            'adv_img' => Yii::t('goods_category', 'Adv Img'),
            'adv_type' => Yii::t('goods_category', 'Adv Type'),
            'adv_value' => Yii::t('goods_category', 'Adv Value'),
            'is_recommend' => Yii::t('goods_category', 'Is Recommend'),
            'status' => Yii::t('goods_category', 'Status'),
            'created_by' => Yii::t('goods_category', 'Created By'),
            'created_at' => Yii::t('goods_category', 'Created At'),
            'updated_by' => Yii::t('goods_category', 'Updated By'),
            'updated_at' => Yii::t('goods_category', 'Updated At'),
        ];
    }

    /**
     * 获取下拉菜单选项
     * @param bool $hasRoot
     * @return array|mixed
     */
    public static function getGoodsCategoryTreeOptions($hasRoot=true)
    {
        $cache = Yii::$app->cache;
        //增加缓存获取
        if($hasRoot){
            $data = $cache->get('goods_category_tree_options');
        }else{
            $data = $cache->get('goods_category_tree_options_children');
        }
        if ($data == false) {
            $options = [];
            //获取顶级不同的树，目前只有一个数了，但是不修改了，也同样适用
            $roots = static::find()->roots()->all();
            if (!empty($roots)) {
                $icon = '';
                $blank = '&nbsp;&nbsp;&nbsp;';
                foreach ($roots as $root) {
                    if($hasRoot){
                        $options[$root->id] = $root->name;
                    }
                    //每个树的子集直接遍历即可，顺序在获取时已经排序好了
                    $children = $root->children()->all();
                    if (!empty($children)) {
                        foreach ($children as $child) {
                            $depth = $child->depth;
                            $blankStr = '';
                            if ($depth > 0) {
                                $blankStr = str_repeat($blank, $child->depth) . $icon;
                            }
                            $options[$child->id] = $blankStr . $child->name;
                        }
                    }
                }
            }
            $data = $options;
            //写入缓存
            $dependency = new DbDependency(['sql' => 'SELECT max(updated_at) FROM ' . static::tableName()]);
            if($hasRoot) {
                $cache->set('goods_category_tree_options', $data, 0, $dependency);
            }else{
                $cache->set('goods_category_tree_options_children', $data, 0, $dependency);
            }
        }
        return $data;
    }

    /**
     *  获取广告分类下拉菜单列表或者某一名称
     * @param bool $key
     * @return array|mixed
     */
    public static function getAdvTypeOptions($key = false)
    {
        $arr = [
            self::ADV_TYPE_NONE => Yii::t('goods_category', 'none'),
            self::ADV_TYPE_URL => Yii::t('goods_category', 'url'),
            self::ADV_TYPE_GOODS => Yii::t('goods_category', 'goods'),
            self::ADV_TYPE_ARTICLE => Yii::t('goods_category', 'article'),
        ];
        return $key === false ? $arr : ArrayHelper::getValue($arr, $key, Yii::t('common', 'Unknown'));
    }

    /**
     *  获取广告分类下拉菜单列表或者某一名称
     * @param bool $key
     * @return array|mixed
     */
    public static function getRecommendOptions($key = false)
    {
        $arr = [
            self::RECOMMEND_OFF => Yii::t('goods_category', 'UnRecommend'),
            self::RECOMMEND_ON => Yii::t('goods_category', 'Recommend'),
        ];
        return $key === false ? $arr : ArrayHelper::getValue($arr, $key, Yii::t('common', 'Unknown'));
    }

    /**
     *  获取状态下拉菜单列表或者某一名称
     * @param bool $key
     * @return array|mixed
     */
    public static function getStatusOptions($key = false)
    {
        $arr = [
            self::STATUS_HIDE => Yii::t('common', 'Hide'),
            self::STATUS_VISIBLE => Yii::t('common', 'Visible')
        ];
        return $key === false ? $arr : ArrayHelper::getValue($arr, $key, Yii::t('common', 'Unknown'));
    }

    /**
     * 暂未用到
     *  https://gist.github.com/ptheofan/d6760ebbaf2371c75c62
     * Convert a tree into nested arrays. If you use the default function parameters you get
     * a set compatible with Yii2 Menu widget.
     *
     * @param int $depth
     * @param string $itemsKey
     * @param callable|null $getDataCallback
     * @return array
     */
    public function toNestedArray($depth = null, $itemsKey = 'items', $getDataCallback = null)
    {
        /** @var GoodsCategory $nodes */
        $nodes = $this->children($depth)->all();
        $exportedAttributes = array_diff(array_keys($this->attributes), ['lft', 'rgt']);

        $trees = [];
        $stack = [];

        foreach ($nodes as $node) {
            if ($getDataCallback) {
                $item = call_user_func($getDataCallback, $node);
            } else {
                $item = $node->toArray($exportedAttributes);
            }

            $item[$itemsKey] = [];
            $l = count($stack);

            while ($l > 0 && $stack[$l - 1]['depth'] >= $item['depth']) {
                array_pop($stack);
                $l--;
            }

            if ($l == 0) {
                // Assign root node
                $i = count($trees);
                $trees[$i] = $item;
                $stack[] = &$trees[$i];
            } else {
                // Add node to parent
                $i = count($stack[$l - 1][$itemsKey]);
                $stack[$l - 1][$itemsKey][$i] = $item;
                $stack[] = &$stack[$l - 1][$itemsKey][$i];
            }
        }

        return $trees;
    }

    /**
     * 同上
     * Export NestedSets tree into JsTree nested format data
     * @return array
     */
    public static function asJsTree()
    {
        $rVal = [];

        /** @var GoodsCategory[] $roots */
        $roots = static::find()->roots()->all();
        foreach ($roots as $root) {
            $rVal[] = [
                'id' => $root->id,
                'text' => $root->name,
                'depth' => $root->depth,
                'children' => $root->toNestedArray(null, 'children', function ($node) {
                    return [
                        'id' => $node->id,
                        'text' => $node->name,
                        'depth' => $node->depth,
                    ];
                }),
            ];
        }
        return $rVal;
    }
}
