<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 09/11/2018
 * Time: 15:09
 */

use yii\db\Migration;

class m160408_074835_create_filter_pref_user extends Migration
{
    public function up()
    {
        $this->createTable('filter_pref_user', [
            'id' => $this->primaryKey(),
            'id_user' => $this->integer(11)->notNull(),
            'id_service' => $this->integer(11)->notNull(),
            'id_germe' => $this->integer(11)->notNull(),
            'id_model' => $this->integer(11)->notNull(),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('filter_pref_user');
    }
}