<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 09/10/2018
 * Time: 11:57
 */

use yii\db\Migration;

class m160408_074825_create_labo extends Migration
{
    public function up()
    {
        $this->createTable('laboratoires', [
            'id' => $this->primaryKey(),
            'raison_sociale' => $this->string(255)->notNull(),
            'adresse' => $this->string(255)->defaultValue(NULL),
            'code_postal' => $this->string(10)->defaultValue(NULL),
            'ville' => $this->string(80)->defaultValue(NULL),
            'tel' => $this->string(25)->defaultValue(NULL),
            'fax' => $this->string(25)->defaultValue(NULL),
            'email' => $this->string(50)->defaultValue(NULL),
            'web' => $this->string(50)->defaultValue(NULL),
            'user_create' => $this->integer(11)->notNull(),
            'date_create' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'cofrac' => $this->boolean()->defaultValue(NULL),
            'active' => $this->boolean()->defaultValue(NULL),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('laboratoires');
    }
}