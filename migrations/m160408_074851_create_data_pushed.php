<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 27/11/2018
 * Time: 15:29
 */

use yii\db\Migration;

class m160408_074851_create_data_pushed extends Migration
{
    public function up()
    {
        $this->createTable('data_pushed', [
            'id' => $this->primaryKey(),
            'id_user' => $this->integer(11)->notNull(),
            'filename' => $this->string(80)->notNull(),
            'id_labo' => $this->integer(11)->notNull(),
            'id_parent' => $this->integer(11)->notNull(),
            'id_client' => $this->integer(11)->notNull(),
            'nb_lignes' => $this->integer(11)->notNull(),
            'last_push' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('data_pushed');
    }
}