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
        $this->batchInsert('analyse_interpretation',['id', 'libelle'],[
            [1, 'A changer'],
            [2, 'Absence de conclusion.'],
            [3, 'Bon'],
            [4, 'Bonne'],
            [5, 'Conforme'],
            [6, 'Eau bactériologiquement non potable.'],
            [7, 'Eau bactériologiquement potable.'],
            [8, 'Echantillon acceptable.'],
            [9, 'Echantillon de qualité acceptable.'],
            [10, 'Echantillon de qualité non satisfaisante.'],
            [11, 'Echantillon de qualité satisfaisante.'],
            [12, 'Echantillon non satisfaisant.'],
            [13, 'Echantillon satisfaisant'],
            [14, 'Limite'],
            [15, 'Mauvais'],
            [16, 'Médiocre'],
            [17, 'Non conforme'],
            [18, 'Passable'],
            [19, 'Résultat conforme aux critères : <100mg/kg (Règl.CE n°2073/2005 mod)'],
            [20, 'Satisfaisant à surveiller présence de Listéria Monocytogènes.'],
            [21, 'Très bon'],
            [22, 'Très mauvais']
        ]);
    }

    public function down()
    {
        echo "m160408_074842_insert_analyse_interpretation cannot be reverted.\n";

        return false;
    }
}