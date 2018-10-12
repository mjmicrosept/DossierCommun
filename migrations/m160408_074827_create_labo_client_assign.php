<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 12/10/2018
 * Time: 14:02
 */

use yii\db\Migration;

class m160408_074827_create_labo_client_assign extends Migration
{
    public function up()
    {
        $this->createTable('labo_client_assign', [
            'id' => $this->primaryKey(),
            'id_labo' => $this->integer(11)->notNull(),
            'id_client' => $this->integer(11)->notNull(),
            'user_create' => $this->integer(11)->notNull(),
            'date_create' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'assign' => $this->boolean()->defaultValue(false),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('labo_client_assign');
    }

}