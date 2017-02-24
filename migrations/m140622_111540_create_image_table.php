<?php

/**
 * Class m140622_111540_create_image_table
 */
class m140622_111540_create_image_table extends \yii\db\Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%image}}', [
            'id'        => 'pk',
            'filePath'  => 'VARCHAR(400) NOT NULL',
            'itemId'    => 'int(20) NOT NULL',
            'isMain'    => 'int(1)',
            'modelName' => 'VARCHAR(150) NOT NULL',
            'urlAlias'  => 'VARCHAR(400) NOT NULL',
            'name'      => 'VARCHAR(80)',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%image}}');

        return false;
    }
}
