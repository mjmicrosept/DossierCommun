<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 05/11/2018
 * Time: 09:51
 */

use yii\db\Migration;

class m160408_074832_create_analyse_germe extends Migration
{
    public function up()
    {
        $this->createTable('analyse_germe', [
            'id' => $this->primaryKey(),
            'id_service' => $this->integer(11)->notNull(),
            'libelle' => $this->string(255)->defaultValue(NULL),
            'code' => $this->string(50)->notNull(),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('analyse_germe');
    }
}