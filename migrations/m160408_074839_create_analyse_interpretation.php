<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 12/11/2018
 * Time: 17:05
 */

use yii\db\Migration;

class m160408_074839_create_analyse_interpretation extends Migration
{
    public function up()
    {
        $this->createTable('analyse_interpretation', [
            'id' => $this->primaryKey(),
            'id_labo' => $this->integer(11)->notNull(),
            'libelle' => $this->string(255)->notNull(),
            'conforme' => $this->boolean()->notNull(),
            'active' => $this->boolean()->notNull()->defaultValue(1),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('analyse_interpretation');
    }
}