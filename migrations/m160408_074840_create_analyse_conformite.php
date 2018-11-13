<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 12/11/2018
 * Time: 17:06
 */

use yii\db\Migration;

class m160408_074840_create_analyse_conformite extends Migration
{
    public function up()
    {
        $this->createTable('analyse_conformite', [
            'id' => $this->primaryKey(),
            'libelle' => $this->string(255)->notNull(),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('analyse_conformite');
    }
}