<?php

use yii\db\Migration;

class m261007_143628_storage_corrections extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('storage_corrections',
            [
                'id' => $this->primaryKey(),
                'type' => $this->string(255)->notNull(),
                'name' => $this->string(355)->notNull(),
                'shablon_params' => $this->string(255)->notNull(),
                'params' => $this->text()->notNull(),
                'value' => $this->getDb()->getSchema()->createColumnSchemaBuilder('mediumtext')->null(),
                'comment' => $this->text()->null(),
                'shablon_answer' => $this->text()->null(),
                'state' => $this->integer(1)->notNull()->defaultValue(2),
                'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
                'last_updated_command' => $this->timestamp()->null(),
            ]);
        $this->createIndex('idx_type', 'storage_corrections', 'type');
        $this->createIndex('idx_name', 'storage_corrections', 'name');
        $this->createIndex('idx_state', 'storage_corrections', 'state');
        $this->createIndex('idx_created_at', 'storage_corrections', 'created_at');
        $this->createIndex('idx_last_updated_command', 'storage_corrections', 'last_updated_command');
        $this->createIndex('idx_type_state', 'storage_corrections', ['type', 'state']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_type', 'storage_corrections');
        $this->dropIndex('idx_name', 'storage_corrections');
        $this->dropIndex('idx_state', 'storage_corrections');
        $this->dropIndex('idx_created_at', 'storage_corrections');
        $this->dropIndex('idx_last_updated_command', 'storage_corrections');
        $this->dropIndex('idx_type_state', 'storage_corrections');

        $this->dropTable('storage_corrections');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m261007_143628_storage_corrections cannot be reverted.\n";

        return false;
    }
    */
}
