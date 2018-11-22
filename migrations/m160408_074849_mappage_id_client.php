<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 21/11/2018
 * Time: 14:21
 */

use yii\db\Migration;

class m160408_074849_mappage_id_client extends Migration
{
    public function up()
    {
        $this->createTable('mappage_id_client', [
            'id' => $this->primaryKey(),
            'id_labo' => $this->integer(11)->notNull(),
            'id_portail_client' => $this->integer(11)->notNull(),
            'id_lims_client' => $this->integer(11)->notNull(),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('mappage_id_client');
    }
}