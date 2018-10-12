<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 12/10/2018
 * Time: 10:14
 */

use yii\db\Migration;

class m160408_074826_create_client_dossier extends Migration
{
    public function up()
    {
        $this->createTable('client_dossier', [
            'id' => $this->primaryKey(),
            'id_client' => $this->integer(11)->notNull(),
            'dossier_name' => $this->string(50)->notNull(),
            'user_create' => $this->integer(11)->notNull(),
            'date_create' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'archive' => $this->boolean()->defaultValue(false),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('client_dossier');
    }
}