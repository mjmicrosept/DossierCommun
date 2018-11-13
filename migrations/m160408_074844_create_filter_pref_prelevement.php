<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 13/11/2018
 * Time: 15:43
 */

use yii\db\Migration;

class m160408_074844_create_filter_pref_prelevement extends Migration
{
    public function up()
    {
        $this->createTable('filter_pref_prelevement', [
            'id' => $this->primaryKey(),
            'id_user' => $this->integer(11)->notNull(),
            'id_conditionnement' => $this->integer(11)->notNull(),
            'id_lieu_prelevement' => $this->integer(11)->notNull(),
            'id_model' => $this->integer(11)->notNull(),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('filter_pref_prelevement');
    }
}