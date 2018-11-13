<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 12/11/2018
 * Time: 13:48
 */

use yii\db\Migration;

class m160408_074837_create_analyse_data extends Migration
{
    public function up()
    {
        $this->createTable('analyse_data', [
            'id' => $this->primaryKey(),
            'num_analyse' => $this->integer(30)->notNull(),
            'id_labo' => $this->integer(11)->notNull(),
            'id_client' => $this->integer(11)->notNull(),
            'id_parent' => $this->integer(11)->notNull(),
            'id_service' => $this->integer(11)->notNull(),
            'id_interpretation' => $this->integer(11)->notNull(),
            'id_conformite' => $this->integer(11)->notNull(),
            'designation' => $this->string(255)->notNull(),
            'date_analyse' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('analyse_data');
    }
}