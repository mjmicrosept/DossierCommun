<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 12/11/2018
 * Time: 09:52
 */

use yii\db\Migration;

class m160408_074836_create_filter_model extends Migration
{
    public function up()
    {
        $this->createTable('filter_model', [
            'id' => $this->primaryKey(),
            'type' => $this->integer(11)->notNull(),
            'id_user' => $this->integer(11)->notNull(),
            'libelle' => $this->string(80)->notNull(),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('filter_model');
    }
}