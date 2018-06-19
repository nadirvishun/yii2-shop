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
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB COMMENT="商品分类"';
        }

        $this->createTable(self::TBL_NAME, [
            'id' => $this->primaryKey(),
            'tree' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(0)->comment('多个树标识'),
            'name' => $this->string()->notNull()->defaultValue('')->comment('分类名称'),
            'img' => $this->string()->notNull()->defaultValue('')->comment('分类图片'),
            'lft' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('左值'),
            'rgt' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('右值'),
            'depth' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('深度'),
            'adv_img' => $this->string()->notNull()->defaultValue('')->comment('广告图片'),
            'adv_type' => $this->integer()->unsigned()->notNull()->defaultValue(1)->comment('广告跳转类型，1:url,2:goods等'),
            'adv_value' => $this->string()->notNull()->defaultValue('')->comment('广告跳转值'),
            'sort' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'is_recommend' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('是否推荐，0否，1是'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(1)->comment('状态:0隐藏，1显示'),
            'created_by' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('创建人'),
            'created_at' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('创建时间'),
            'updated_by' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('更新人'),
            'updated_at' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment('更新时间')
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
