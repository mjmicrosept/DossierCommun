<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 08/10/2018
 * Time: 11:46
 */

use yii\db\Migration;

class m160408_074824_create_portail_users extends Migration
{
    public function up()
    {
        $this->createTable('portail_users', [
            'id' => $this->primaryKey(),
            'id_user' => $this->integer(11)->notNull(),
            'id_client' => $this->integer(11)->defaultValue(NULL),
            'id_labo' => $this->integer(11)->defaultValue(NULL),
            'date_create' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

    }

    public function down()
    {
        $this->dropTable('portail_users');
    }
}