<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 13/11/2018
 * Time: 15:39
 */

use yii\db\Migration;

class m160408_074843_create_filter_pref_keyword extends Migration
{
    public function up()
    {
        $this->createTable('filter_pref_keyword', [
            'id' => $this->primaryKey(),
            'id_user' => $this->integer(11)->notNull(),
            'keyword' => $this->string(80)->notNull(),
            'id_model' => $this->integer(11)->notNull(),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('filter_pref_keyword');
    }
}