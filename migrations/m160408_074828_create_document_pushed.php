<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 19/10/2018
 * Time: 09:18
 */

use yii\db\Migration;

class m160408_074828_create_document_pushed extends Migration
{
    public function up()
    {
        $this->createTable('document_pushed', [
            'id' => $this->primaryKey(),
            'id_labo' => $this->integer(11)->notNull(),
            'id_client' => $this->integer(11)->notNull(),
            'year' => $this->integer(11)->notNull(),
            'month' => $this->integer(11)->notNull(),
            'nb_doc' => $this->integer(11)->notNull(),
            'last_push' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('document_pushed');
    }
}