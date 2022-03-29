<?php

use yii\db\Migration;

/**
 * Class m220325_114950_inser_admin
 */
class m220325_114950_inser_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $signupMatteo = new \frontend\models\SignupForm([
            'username' => 'matteo.maiocchi',
            'email' => 'matteo.maiocchi@dieffe.tech',
            'password' => 'password',
        ]);

        $signupMatteo->signup();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220325_114950_inser_admin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220325_114950_inser_admin cannot be reverted.\n";

        return false;
    }
    */
}
