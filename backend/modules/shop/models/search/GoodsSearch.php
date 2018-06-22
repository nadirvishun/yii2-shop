<?php

namespace backend\modules\shop\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\shop\models\Goods;

/**
 * GoodsSearch represents the model behind the search form about `backend\modules\shop\models\Goods`.
 */
class GoodsSearch extends Goods
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'brand_id', 'price', 'market_price', 'cost_price', 'sales', 'real_sales', 'click', 'collect', 'stock', 'stock_alarm', 'stock_type', 'is_freight_free', 'freight_type', 'freight_id', 'freight_price', 'is_new', 'is_hot', 'is_recommend', 'is_limit', 'max_buy', 'min_buy', 'user_max_buy', 'give_integral', 'sort', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['goods_sn', 'goods_barcode', 'title', 'sub_title', 'unit', 'img', 'img_others', 'content'], 'safe'],
            [['weight'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Goods::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'pagination' => [
                //'pageSize' => 20,
            //],
            //'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'price' => $this->price,
            'market_price' => $this->market_price,
            'cost_price' => $this->cost_price,
            'sales' => $this->sales,
            'real_sales' => $this->real_sales,
            'click' => $this->click,
            'collect' => $this->collect,
            'stock' => $this->stock,
            'stock_alarm' => $this->stock_alarm,
            'stock_type' => $this->stock_type,
            'weight' => $this->weight,
            'is_freight_free' => $this->is_freight_free,
            'freight_type' => $this->freight_type,
            'freight_id' => $this->freight_id,
            'freight_price' => $this->freight_price,
            'is_new' => $this->is_new,
            'is_hot' => $this->is_hot,
            'is_recommend' => $this->is_recommend,
            'is_limit' => $this->is_limit,
            'max_buy' => $this->max_buy,
            'min_buy' => $this->min_buy,
            'user_max_buy' => $this->user_max_buy,
            'give_integral' => $this->give_integral,
            'sort' => $this->sort,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'goods_sn', $this->goods_sn])
            ->andFilterWhere(['like', 'goods_barcode', $this->goods_barcode])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'sub_title', $this->sub_title])
            ->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'img', $this->img])
            ->andFilterWhere(['like', 'img_others', $this->img_others])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
