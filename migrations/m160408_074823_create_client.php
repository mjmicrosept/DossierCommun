<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 08/10/2018
 * Time: 11:46
 */

use yii\db\Migration;

class m160408_074823_create_client extends Migration
{
    public function up()
    {
        $this->createTable('client', [
            'id' => $this->primaryKey(),
            'id_parent' => $this->integer(11),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'user_create' => $this->integer(11)->notNull(),
            'date_create' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'active' => $this->boolean()->defaultValue(NULL),
            'is_parent' => $this->boolean()->defaultValue(1),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('client');
    }
}