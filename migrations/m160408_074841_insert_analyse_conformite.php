<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 12/11/2018
 * Time: 17:20
 */

use yii\db\Migration;

class m160408_074841_insert_analyse_conformite extends Migration
{
    public function up()
    {
        $this->batchInsert('analyse_conformite',['id', 'libelle'],[
            [1, 'Conforme'],
            [2, 'Non conforme']
        ]);
    }

    public function down()
    {
        echo "m160408_074841_insert_analyse_conformite cannot be reverted.\n";

        return false;
    }
}