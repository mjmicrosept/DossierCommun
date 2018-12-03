<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 30/11/2018
 * Time: 17:13
 */

use yii\db\Migration;

class m160408_074852_create_log_labo_documents_delete extends Migration
{
    public function up()
    {
        $this->createTable('log_labo_documents_delete', [
            'id' => $this->primaryKey(),
            'id_user' => $this->integer(11)->notNull(),
            'id_labo' => $this->integer(11)->notNull(),
            'id_client' => $this->integer(11)->notNull(),
            'id_etablissement' => $this->integer(11)->notNull(),
            'year' => $this->integer(11)->notNull(),
            'month' => $this->integer(11)->notNull(),
            'raison' => $this->string(255)->notNull(),
            'filename' => $this->string(255)->notNull(),
            'log_date' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('log_labo_documents_delete');
    }
}