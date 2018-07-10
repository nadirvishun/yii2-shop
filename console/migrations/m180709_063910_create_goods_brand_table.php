<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_brand`.
 */
class m180709_063910_create_goods_brand_table extends Migration
{
    const TBL_NAME = '{{%goods_brand}}';

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
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB COMMENT="商品品牌"';
        }

        $this->createTable(self::TBL_NAME, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->defaultValue('')->comment('品牌名称'),
            'initial' => $this->string(1)->notNull()->defaultValue('')->comment('品牌首字母'),
            'category_id' => $this->integer()->notNull()->defaultValue(0)->comment('所属分类ID'),
            'img' => $this->string()->notNull()->defaultValue('')->comment('品牌图片'),
            'content' => $this->text()->notNull()->comment('品牌详情'),
            'is_recommend' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('是否推荐，0否，1是'),
            'sort' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('状态:0隐藏，1显示'),
            'created_by' => $this->integer()->notNull()->defaultValue(0)->comment('创建人'),
            'created_at' => $this->bigInteger()->notNull()->defaultValue(0)->comment('创建时间'),
            'updated_by' => $this->integer()->notNull()->defaultValue(0)->comment('更新人'),
            'updated_at' => $this->bigInteger()->notNull()->defaultValue(0)->comment('更新时间')
        ], $tableOptions);

        //创建索引
        $this->createIndex('is_recommend', self::TBL_NAME, 'is_recommend');
        $this->createIndex('status', self::TBL_NAME, 'status');

        //增加后台菜单显示
        $rootId = (new \yii\db\Query())->select('id')
            ->from('{{%backend_menu}}')
            ->where(['url' => 'goods'])
            ->scalar();
        if (!empty($rootId)) {
            $time = time();
            $this->insert("{{%backend_menu}}", [
                'pid' => $rootId,
                'name' => '商品品牌',
                'url' => 'shop/goods-brand/index',
                'icon' => 'circle-o',
                'sort' => 2,
                'created_by' => 1,
                'created_at' => time(),
                'updated_by' => 1,
                'updated_at' => time(),
                'status' => 1
            ]);
            $insertId = $this->db->lastInsertID;
            $this->batchInsert("{{%backend_menu}}", ['pid', 'name', 'url', 'icon', 'sort', 'created_by', 'created_at', 'updated_by', 'updated_at', 'status'], [
                [$insertId, '新增品牌', 'shop/goods-brand/create', '', 3, 1, $time, 1, $time, 0],
                [$insertId, '修改品牌', 'shop/goods-brand/update', '', 2, 1, $time, 1, $time, 0],
                [$insertId, '删除品牌', 'shop/goods-brand/delete', '', 1, 1, $time, 1, $time, 0],
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        //删除menu增加项目
        $this->delete("{{%backend_menu}}", ['in', 'url', [
            'shop/goods-brand/index',
            'shop/goods-brand/create',
            'shop/goods-brand/update',
            'shop/goods-brand/delete'
        ]]);

        $this->dropTable(self::TBL_NAME);
    }
}
