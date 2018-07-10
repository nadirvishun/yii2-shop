<?php

namespace backend\modules\shop\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%goods_brand}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $initial
 * @property integer $category_id
 * @property string $img
 * @property string $content
 * @property integer $is_recommend
 * @property integer $sort
 * @property integer $status
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 */
class GoodsBrand extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_brand}}';
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
            [['category_id', 'is_recommend', 'sort', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['name','initial'], 'required'],
            [['content'], 'string'],
            [['name', 'img'], 'string', 'max' => 191],
            [['initial'], 'string', 'max' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('goods_brand', 'ID'),
            'name' => Yii::t('goods_brand', 'Name'),
            'initial' => Yii::t('goods_brand', 'Initial'),
            'category_id' => Yii::t('goods_brand', 'Category ID'),
            'img' => Yii::t('goods_brand', 'Img'),
            'content' => Yii::t('goods_brand', 'Content'),
            'is_recommend' => Yii::t('goods_brand', 'Is Recommend'),
            'sort' => Yii::t('goods_brand', 'Sort'),
            'status' => Yii::t('goods_brand', 'Status'),
            'created_by' => Yii::t('goods_brand', 'Created By'),
            'created_at' => Yii::t('goods_brand', 'Created At'),
            'updated_by' => Yii::t('goods_brand', 'Updated By'),
            'updated_at' => Yii::t('goods_brand', 'Updated At'),
        ];
    }
}
