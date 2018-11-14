<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 14/11/2018
 * Time: 10:05
 */

use yii\db\Migration;

class m160408_074847_create_analyse_lieu_prelevement extends Migration
{
    public function up()
    {
        $this->createTable('analyse_lieu_prelevement', [
            'id' => $this->primaryKey(),
            'libelle' => $this->string(80)->notNull(),
            'active' => $this->boolean()->notNull()->defaultValue(1),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('analyse_lieu_prelevement');
    }
}