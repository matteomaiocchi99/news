<?php

use yii\db\Migration;

/**
 * Class m220325_114646_prova_con_teo
 */
class m220325_114646_prova_con_teo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('prova_teo',[
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220325_114646_prova_con_teo cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220325_114646_prova_con_teo cannot be reverted.\n";

        return false;
    }
    */
}
