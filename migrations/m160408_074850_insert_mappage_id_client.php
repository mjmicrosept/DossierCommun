<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 21/11/2018
 * Time: 14:27
 */

use yii\db\Migration;

class m160408_074850_insert_mappage_id_client extends Migration
{
    public function up()
    {
        $this->batchInsert('mappage_id_client',['id', 'id_labo','id_portail_client','id_lims_client'],[
            [1, 3,20,5607],
            [2, 3,16,3750],
            [3, 3,31,7058],
            [4, 3,30,6953],
            [5, 3,18,5606],
            [6, 3,22,5775],
            [7, 3,36,7526]
        ]);
    }

    public function down()
    {
        echo "m160408_074850_insert_mappage_id_client cannot be reverted.\n";

        return false;
    }
}