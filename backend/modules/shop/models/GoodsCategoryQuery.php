<?php

namespace backend\modules\shop\models;
use creocoder\nestedsets\NestedSetsQueryBehavior;

/**
 * This is the ActiveQuery class for [[GoodsCategory]].
 *
 * @see GoodsCategory
 */
class GoodsCategoryQuery extends \yii\db\ActiveQuery
{
    public function behaviors()
    {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }
}
