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
        $this->batchInsert('analyse_interpretation',['id', 'libelle','conforme','active'],[
            [1, 'A changer',3,1],
            [2, 'Absence de conclusion.',3,1],
            [3, 'Bon',3,1],
            [4, 'Bonne',3,1],
            [5, 'Conforme',1,1],
            [6, 'Eau bactériologiquement non potable.',2,1],
            [7, 'Eau bactériologiquement potable.',1,1],
            [8, 'Echantillon acceptable.',1,1],
            [9, 'Echantillon de qualité acceptable.',1,1],
            [10, 'Echantillon de qualité non satisfaisante.',2,1],
            [11, 'Echantillon de qualité satisfaisante.',1,1],
            [12, 'Echantillon non satisfaisant.',2,1],
            [13, 'Echantillon satisfaisant',1,1],
            [14, 'Limite',3,1],
            [15, 'Mauvais',3,1],
            [16, 'Médiocre',3,1],
            [17, 'Non conforme',2,1],
            [18, 'Passable',3,1],
            [19, 'Résultat conforme aux critères : <100mg/kg (Règl.CE n°2073/2005 mod)',1,1],
            [20, 'Satisfaisant à surveiller présence de Listéria Monocytogènes.',1,1],
            [21, 'Très bon',3,1],
            [22, 'Très mauvais',3,1]
        ]);
    }

    public function down()
    {
        echo "m160408_074842_insert_analyse_interpretation cannot be reverted.\n";

        return false;
    }
}