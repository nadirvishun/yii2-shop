<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_param`.
 */
class m180626_005559_create_goods_param_table extends Migration
{
    const TBL_NAME = '{{%goods_param}}';

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
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB COMMENT="商品参数"';
        }

        $this->createTable(self::TBL_NAME, [
            'id' => $this->primaryKey(),
            'goods_id' => $this->integer()->notNull()->defaultValue(0)->comment('相关商品ID'),
            'name' => $this->string()->notNull()->defaultValue('')->comment('参数名称'),
            'value' => $this->string()->notNull()->defaultValue('')->comment('参数值'),
            'sort' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'created_by' => $this->integer()->notNull()->defaultValue(0)->comment('创建人'),
            'created_at' => $this->bigInteger()->notNull()->defaultValue(0)->comment('创建时间'),
            'updated_by' => $this->integer()->notNull()->defaultValue(0)->comment('更新人'),
            'updated_at' => $this->bigInteger()->notNull()->defaultValue(0)->comment('更新时间')
        ], $tableOptions);
        //创建索引
        $this->createIndex('goods_id', self::TBL_NAME, 'goods_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
