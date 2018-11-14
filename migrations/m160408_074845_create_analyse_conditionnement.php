<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 14/11/2018
 * Time: 09:34
 */

use yii\db\Migration;

class m160408_074845_create_analyse_conditionnement extends Migration
{
    public function up()
    {
        $this->createTable('analyse_conditionnement', [
            'id' => $this->primaryKey(),
            'libelle' => $this->string(80)->notNull(),
            'active' => $this->boolean()->notNull()->defaultValue(1),
        ]);

        //$this->createIndex('societe_user_create','client',['user_create']);
    }

    public function down()
    {
        $this->dropTable('analyse_conditionnement');
    }
}