<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 24/10/2018
 * Time: 14:56
 */

use yii\db\Migration;

class m160408_074829_create_document_alerte extends Migration
{
    public function up()
    {
        $this->createTable('document_alerte', [
            'id' => $this->primaryKey(),
            'id_hashed' => $this->string(255)->defaultValue(NULL),
            'id_labo' => $this->integer(11)->notNull(),
            'id_client' => $this->integer(11)->notNull(),
            'id_etablissement' => $this->integer(11),
            'id_user' => $this->integer(11)->notNull(),
            'type' => $this->integer(11)->notNull(),
            'type_emetteur' => $this->integer(11)->notNull(),
            'vecteur' => $this->integer(11)->notNull(),
            'year_missing' => $this->integer(11),
            'month_missing' => $this->integer(11),
            'year_corrupted' => $this->integer(11),
            'month_corrupted' => $this->integer(11),
            'year_nocontext' => $this->integer(11),
            'month_nocontext' => $this->integer(11),
            'periode_missing' => $this->integer(11),
            'date_create' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'date_update' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'vue' => $this->boolean()->defaultValue(false),
            'active' => $this->boolean()->defaultValue(true),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('document_alerte');
    }
}