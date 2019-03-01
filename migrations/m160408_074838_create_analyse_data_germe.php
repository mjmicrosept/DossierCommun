<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 12/11/2018
 * Time: 13:49
 */

use yii\db\Migration;

class m160408_074838_create_analyse_data_germe extends Migration
{
    public function up()
    {
        $this->createTable('analyse_data_germe', [
            'id' => $this->primaryKey(),
            'id_analyse' => $this->integer(11)->notNull(),
            'libelle' => $this->string(255)->notNull(),
            'resultat' => $this->string(50),
            'expression' => $this->string(50),
            'interpretation' => $this->string(255),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('analyse_data_germe');
    }
}