<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_sku`.
 */
class m180629_004921_create_goods_sku_table extends Migration
{
    const TBL_NAME = '{{%goods_sku}}';

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
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB COMMENT="商品SKU"';
        }

        $this->createTable(self::TBL_NAME, [
            'id' => $this->primaryKey(),
            'goods_id' => $this->integer()->notNull()->defaultValue(0)->comment('相关商品ID'),
            'goods_specs' => $this->text()->notNull()->comment('SKU包含的规格内容'),
            'goods_sn' => $this->string(100)->notNull()->defaultValue('')->comment('SKU商品编号'),
            'goods_barcode' => $this->string(100)->notNull()->defaultValue('')->comment('SKU商品条形码'),
            'price' => $this->integer()->notNull()->defaultValue(0)->comment('商品价格'),
            'market_price' => $this->integer()->notNull()->defaultValue(0)->comment('市场价格'),
            'cost_price' => $this->integer()->notNull()->defaultValue(0)->comment('成本价格'),
            'stock' => $this->integer()->notNull()->defaultValue(0)->comment('库存数量'),
            'stock_alarm' => $this->integer()->notNull()->defaultValue(0)->comment('库存预警数量'),
            'weight' => $this->decimal(10, 2)->notNull()->defaultValue(0)->comment('商品重量，单位克'),
            'created_by' => $this->integer()->notNull()->defaultValue(0)->comment('创建人'),
            'created_at' => $this->bigInteger()->notNull()->defaultValue(0)->comment('创建时间'),
            'updated_by' => $this->integer()->notNull()->defaultValue(0)->comment('更新人'),
            'updated_at' => $this->bigInteger()->notNull()->defaultValue(0)->comment('更新时间')
        ], $tableOptions);

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
