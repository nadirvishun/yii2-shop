<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods`.
 */
class m180621_020819_create_goods_table extends Migration
{
    const TBL_NAME = '{{%goods}}';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            //获取mysql版本
            $version = $this->db->getServerVersion();
            //utf8mb4在小于5.5.3的mysql版本中不支持
            if (version_compare($version, '5.5.3', '<')) {
                throw new \yii\base\Exception('Character utf8mb4 is not supported in mysql < 5.5.3');
            }
            //如果mysql数据库版本小于5.7.7，则需要将varchar默认值修改为191，否则报错：Specified key was too long error
            if (version_compare($version, '5.7.7', '<')) {
                $queryBuilder = $this->db->getQueryBuilder();
                $queryBuilder->typeMap[\yii\db\mysql\Schema::TYPE_STRING] = 'varchar(191)';
            }
            //如果是用utf8字符集，则不需要上面的两个判定
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB AUTO_INCREMENT=101010 COMMENT="商品表"';
        }

        $this->createTable(self::TBL_NAME, [
            'id' => $this->primaryKey(),
            'goods_sn' => $this->string(100)->notNull()->defaultValue('')->comment('商品编码'),
            'goods_barcode' => $this->string(100)->notNull()->defaultValue('')->comment('商品条形码'),
            'title' => $this->string()->notNull()->defaultValue('')->comment('商品标题'),
            'sub_title' => $this->string()->notNull()->defaultValue('')->comment('商品副标题'),
            'category_id' => $this->integer()->notNull()->defaultValue(0)->comment('分类ID'),
            'brand_id' => $this->integer()->notNull()->defaultValue(0)->comment('品牌ID'),
            'price' => $this->integer()->notNull()->defaultValue(0)->comment('商品价格'),
            'unit' => $this->string(10)->notNull()->defaultValue('')->comment('商品单位'),
            'market_price' => $this->integer()->notNull()->defaultValue(0)->comment('市场价格'),
            'cost_price' => $this->integer()->notNull()->defaultValue(0)->comment('成本价格'),
            'img' => $this->string()->notNull()->defaultValue('')->comment('商品图片'),
            'img_others' => $this->text()->notNull()->comment('商品组图'),
            'content' => $this->text()->notNull()->comment('商品详情'),
            'sales' => $this->integer()->notNull()->defaultValue(0)->comment('显示销量'),
            'real_sales' => $this->integer()->notNull()->defaultValue(0)->comment('实际销量'),
            'click' => $this->integer()->notNull()->defaultValue(0)->comment('点击查看量'),
            'collect' => $this->integer()->notNull()->defaultValue(0)->comment('收藏量'),
            'stock' => $this->integer()->notNull()->defaultValue(0)->comment('库存数量'),
            'stock_alarm' => $this->integer()->notNull()->defaultValue(0)->comment('库存预警数量'),
            'stock_type' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('减库存方式：0不减库存，1拍下减库存，2付款减库存'),
            'weight' => $this->decimal(10, 2)->notNull()->defaultValue(0)->comment('商品重量，单位克'),
            'is_freight_free' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('是否包邮，0否，1是'),
            'freight_type' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('邮费方式：0运费模板，1统一运费'),
            'freight_id' => $this->integer()->notNull()->defaultValue(0)->comment('运费模板ID'),
            'freight_price' => $this->integer()->notNull()->defaultValue(0)->comment('统一运费价格'),
            'is_new' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('是否新品，0否，1是'),
            'is_hot' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('是否热卖，0否，1是'),
            'is_recommend' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('是否推荐，0否，1是'),
            'is_limit' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('是否限购，0否，1是'),
            'max_buy' => $this->integer()->notNull()->defaultValue(0)->comment('单次最多购买，0为不限制'),
            'min_buy' => $this->integer()->notNull()->defaultValue(0)->comment('单次最少购买，0为不限制'),
            'user_max_buy' => $this->integer()->notNull()->defaultValue(0)->comment('每个用户最多购买，0为不限制'),
            'has_spec' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('是否启用规格，0否，1是'),
            'spec_name' => $this->string()->notNull()->defaultValue('')->comment('规格名称，序列化存储'),
            'spec_value' => $this->text()->notNull()->comment('规格值，序列化存储'),
            'give_integral' => $this->integer()->notNull()->defaultValue(0)->comment('赠送积分'),
            'sort' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('状态:0下架，1上架，2删除'),
            'created_by' => $this->integer()->notNull()->defaultValue(0)->comment('创建人'),
            'created_at' => $this->bigInteger()->notNull()->defaultValue(0)->comment('创建时间'),
            'updated_by' => $this->integer()->notNull()->defaultValue(0)->comment('更新人'),
            'updated_at' => $this->bigInteger()->notNull()->defaultValue(0)->comment('更新时间')
        ], $tableOptions);
        //创建索引
        $this->createIndex('is_new', self::TBL_NAME, 'is_new');
        $this->createIndex('is_hot', self::TBL_NAME, 'is_hot');
        $this->createIndex('is_recommend', self::TBL_NAME, 'is_recommend');
        $this->createIndex('category_id', self::TBL_NAME, 'category_id');
        $this->createIndex('brand_id', self::TBL_NAME, 'brand_id');
        $this->createIndex('status', self::TBL_NAME, 'status');
        //增加后他菜单显示
        $rootId=(new \yii\db\Query())->select('id')
            ->from('{{%backend_menu}}')
            ->where(['url'=>'goods'])
            ->scalar();
        if(!empty($rootId)){
            $time = time();
            $this->insert("{{%backend_menu}}", [
                'pid' => $rootId,
                'name' => '上架商品',
                'url' => 'shop/goods/index',
                'icon' => 'circle-o',
                'sort'=>'3',
                'created_by' => 1,
                'created_at' => time(),
                'updated_by' => 1,
                'updated_at' => time(),
            ]);
            $insertId = $this->db->lastInsertID;
            $this->batchInsert("{{%backend_menu}}", ['pid', 'name', 'url', 'icon', 'sort', 'created_by', 'created_at', 'updated_by', 'updated_at', 'status'], [
                [$rootId, '下架商品', 'shop/goods/offline', 'circle-o', 2, 1, $time, 1, $time, 1],
                [$rootId, '回收站商品', 'shop/goods/recycle', 'circle-o', 1, 1, $time, 1, $time, 1],
                [$insertId, '新增商品', 'shop/goods/create', '', 3, 1, $time, 1, $time, 0],
                [$insertId, '修改商品', 'shop/goods/update', '', 2, 1, $time, 1, $time, 0],
                [$insertId, '删除商品', 'shop/goods/delete', '', 1, 1, $time, 1, $time, 0],
            ]);
        }
    }


    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
