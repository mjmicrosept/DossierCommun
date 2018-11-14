<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 14/11/2018
 * Time: 09:55
 */

use yii\db\Migration;

class m160408_074846_insert_analyse_conditionnement extends Migration
{
    public function up()
    {
        $this->batchInsert('analyse_conditionnement',['id', 'libelle','active'],[
            [1, 'Barquette',1],
            [2, 'Barquette plastique + étui carton',1],
            [3, 'Barquette alu',1],
            [4, 'Barquette filmée',1],
            [5, 'Barquette plastique',1],
            [6, 'Barquette sous atmosphère',1],
            [7, 'Barquette sous film',1],
            [8, 'Barquette sous gaz',1],
            [9, 'Barquette sous vide',1],
            [10, 'Boite',1],
            [11, 'Boite x6',1],
            [12, 'Bouteille',1],
            [13, 'Calage + film plastique',1],
            [14, 'Calage plastique + étui carton',1],
            [15, 'Emballage client',1],
            [16, 'Film',1],
            [17, 'Flacon stérile',1],
            [18, 'Flacon stérile 1L + thiosulfate',1],
            [19, 'Flacon stérile 500mL + thiosulfate',1],
            [20, 'Lingette',1],
            [21, 'Papier',1],
            [22, 'Papier vente',1],
            [23, 'Pilulier',1],
            [24, 'Pot',1],
            [25, 'Pot stérile',1],
            [26, 'Sac stérile',1],
            [27, 'Sac x 500g',1],
            [28, 'Sachet',1],
            [29, 'Sachet sous vide',1],
            [30, 'Sachet stérile',1],
            [31, 'Semelle + étui carton',1],
            [32, 'Sous atmosphère',1],
            [33, 'Sous atmosphère modifiée',1],
            [34, 'Sous atmosphère protectrice',1],
            [35, 'Sous film',1],
            [36, 'Sous gaz',1],
            [37, 'Sous vide',1],
            [38, 'Tourtière alu + étui carton',1],
            [39, 'Vrac',1]
        ]);
    }

    public function down()
    {
        echo "m160408_074846_insert_analyse_conditionnement cannot be reverted.\n";

        return false;
    }
}