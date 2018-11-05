<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 05/11/2018
 * Time: 09:51
 */

use yii\db\Migration;

class m160408_074831_create_analyse_service extends Migration
{
    public function up()
    {
        $this->createTable('analyse_service', [
            'id' => $this->primaryKey(),
            'libelle' => $this->string(255)->notNull(),
            'active' => $this->boolean()->defaultValue(1),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('analyse_service');
    }
}