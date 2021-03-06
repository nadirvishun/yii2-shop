<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_category`.
 */
class m180618_135820_create_goods_category_table extends Migration
{
    const TBL_NAME = '{{%goods_category}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
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
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB COMMENT="商品分类"';
        }

        $this->createTable(self::TBL_NAME, [
            'id' => $this->primaryKey(),
            'tree' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('多个树标识'),
            'name' => $this->string()->notNull()->defaultValue('')->comment('分类名称'),
            'img' => $this->string()->notNull()->defaultValue('')->comment('分类图片'),
            'lft' => $this->integer()->notNull()->defaultValue(0)->comment('左值'),
            'rgt' => $this->integer()->notNull()->defaultValue(0)->comment('右值'),
            'depth' => $this->integer()->notNull()->defaultValue(0)->comment('深度'),//不能设置为unsigned，否则还是和treeGrid代码有冲突
            'adv_img' => $this->string()->notNull()->defaultValue('')->comment('广告图片'),
            'adv_type' => $this->integer()->notNull()->defaultValue(0)->comment('广告跳转类型，1:url,2:goods等'),
            'adv_value' => $this->string()->notNull()->defaultValue('')->comment('广告跳转值'),
            'is_recommend' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('是否推荐，0否，1是'),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('状态:0隐藏，1显示'),
            'created_by' => $this->integer()->notNull()->defaultValue(0)->comment('创建人'),
            'created_at' => $this->bigInteger()->notNull()->defaultValue(0)->comment('创建时间'),
            'updated_by' => $this->integer()->notNull()->defaultValue(0)->comment('更新人'),
            'updated_at' => $this->bigInteger()->notNull()->defaultValue(0)->comment('更新时间')
        ], $tableOptions);
        //写入最顶级的分类
        $this->insert(self::TBL_NAME, [
            'name' => '顶级分类',
            'lft' => 1,
            'rgt' => 2,
            'depth' => 0,
            'created_by' => 1,
            'created_at' => time(),
            'updated_by' => 1,
            'updated_at' => time(),
        ]);
        //增加后台菜单的显示
        $time = time();
        $this->insert("{{%backend_menu}}", [
            'pid' => 0,
            'name' => '商品管理',
            'url' => 'goods',
            'icon' => 'shopping-bag',
            'created_by' => 1,
            'created_at' => time(),
            'updated_by' => 1,
            'updated_at' => time(),
        ]);
        //商品分类
        $rootInsertId = $this->db->lastInsertID;
        $this->insert("{{%backend_menu}}", [
            'pid' => $rootInsertId,
            'name' => '商品分类',
            'url' => 'shop/goods-category/index',
            'icon' => 'circle-o',
            'sort' => 5,
            'created_by' => 1,
            'created_at' => time(),
            'updated_by' => 1,
            'updated_at' => time(),
        ]);
        $insertId = $this->db->lastInsertID;
        //商品分类操作
        $this->batchInsert("{{%backend_menu}}", ['pid', 'name', 'url', 'icon', 'sort', 'created_by', 'created_at', 'updated_by', 'updated_at', 'status'], [
            [$insertId, '新增商品分类', 'shop/goods-category/create', '', 3, 1, $time, 1, $time, 0],
            [$insertId, '修改配置设置', 'shop/goods-category/update', '', 2, 1, $time, 1, $time, 0],
            [$insertId, '删除配置管理', 'shop/goods-category/delete', '', 1, 1, $time, 1, $time, 0],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        //删除menu增加项目
        $this->delete("{{%backend_menu}}", ['in', 'url', [
            'goods',
            'shop/goods-category/index',
            'shop/goods-category/create',
            'shop/goods-category/update',
            'shop/goods-category/delete'
        ]]);
        //删除表
        $this->dropTable(self::TBL_NAME);
    }
}
