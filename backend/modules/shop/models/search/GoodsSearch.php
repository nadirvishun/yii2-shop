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
            [['id', 'category_id', 'brand_id', 'price', 'market_price', 'cost_price', 'sales', 'real_sales', 'click', 'collect', 'stock', 'stock_alarm', 'stock_type', 'is_freight_free', 'freight_type', 'freight_id', 'freight_price', 'is_new', 'is_hot', 'is_recommend', 'is_limit', 'max_buy', 'min_buy', 'user_max_buy', 'give_integral','has_spec', 'sort', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
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
     * @param $status
     * @return ActiveDataProvider
     */
    public function search($params, $status = 1)
    {
        $query = Goods::find()->where(['status' => $status]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        //库存预警的选取，由于增加了规格，没有这么简单就能获取了，暂时先不处理了
//        if ($this->stock == Goods::STOCK_ALARM_YES) {
//            $query->andFilterWhere(['and','stock_alarm <> 0','stock <= stock_alarm'])
//                ->orFilterWhere(['=', 'stock', 0]);
//        } elseif ($this->stock == Goods::STOCK_ALARM_NO) {
//            $query->andFilterWhere(['or','stock_alarm = 0','stock > stock_alarm','sotck <> 0']);
//        }

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
//            'status' => $this->status,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'goods_sn', $this->goods_sn])
            ->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}
