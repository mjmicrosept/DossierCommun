<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 12/11/2018
 * Time: 17:20
 */

use yii\db\Migration;

class m160408_074842_insert_analyse_interpretation extends Migration
{
    public function up()
    {
        $this->batchInsert('analyse_interpretation',['id','id_labo', 'libelle','conforme','active'],[
            [1,3, 'A changer',3,1],
            [2,3, 'Absence de conclusion.',3,1],
            [3,3, 'Bon',3,1],
            [4,3, 'Bonne',3,1],
            [5,3, 'Conforme',1,1],
            [6,3, 'Eau bactériologiquement non potable.',2,1],
            [7,3, 'Eau bactériologiquement potable.',1,1],
            [8,3, 'Echantillon acceptable.',1,1],
            [9,3, 'Echantillon de qualité acceptable.',1,1],
            [10,3, 'Echantillon de qualité non satisfaisante.',2,1],
            [11,3, 'Echantillon de qualité satisfaisante.',1,1],
            [12,3, 'Echantillon non satisfaisant.',2,1],
            [13,3, 'Echantillon satisfaisant',1,1],
            [14,3, 'Limite',3,1],
            [15,3, 'Mauvais',3,1],
            [16,3, 'Médiocre',3,1],
            [17,3, 'Non conforme',2,1],
            [18,3, 'Passable',3,1],
            [19,3, 'Résultat conforme aux critères : <100mg/kg (Règl.CE n°2073/2005 mod)',1,1],
            [20,3, 'Satisfaisant à surveiller présence de Listéria Monocytogènes.',1,1],
            [21,3, 'Très bon',3,1],
            [22,3, 'Très mauvais',3,1]
        ]);
    }

    public function down()
    {
        echo "m160408_074842_insert_analyse_interpretation cannot be reverted.\n";

        return false;
    }
}