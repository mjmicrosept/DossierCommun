<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 05/11/2018
 * Time: 09:55
 */

use yii\db\Migration;

class m160408_074833_create_labo_correspondance_germe extends Migration
{
    public function up()
    {
        $this->createTable('labo_correspondance_germe', [
            'id' => $this->primaryKey(),
            'id_germe' => $this->integer(11)->notNull(),
            'id_labo' => $this->integer(11)->notNull(),
            'code_labo' => $this->string(50)->notNull(),
            'code_germe' => $this->string(50)->notNull(),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('labo_correspondance_germe');
    }
}