<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 05/11/2018
 * Time: 10:03
 */

use yii\db\Migration;

class m160408_074833_insert_analyse_service extends Migration
{
    public function up()
    {
        $this->batchInsert('analyse_service',['id', 'libelle','active'],[
            [1, 'Cosmétologie',1],
            [2, 'Eaux',1],
            [3, 'Hygiène alimentaire',1],
            [4, 'Pharmacopée',1],
            [5, 'Générique',1]
        ]);
    }

    public function down()
    {
        echo "m160411_101559_insert_analyse_service cannot be reverted.\n";

        return false;
    }
}