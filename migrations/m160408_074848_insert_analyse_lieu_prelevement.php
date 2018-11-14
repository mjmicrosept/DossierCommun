<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 14/11/2018
 * Time: 10:07
 */

use yii\db\Migration;

class m160408_074848_insert_analyse_lieu_prelevement extends Migration
{
    public function up()
    {
        $this->batchInsert('analyse_lieu_prelevement',['id', 'libelle','active'],[
            [1, 'Armoire',1],
            [2, 'Armoire chaude',1],
            [3, 'Cellule de refroidissement',1],
            [4, 'Chambre froide',1],
            [5, 'Chambre froide carcasses',1],
            [6, 'Chambre froide congélation',1],
            [7, 'Chambre froide expédition',1],
            [8, 'Chambre froide négative',1],
            [9, 'Chambre froide positive',1],
            [10, 'Chambre froide Produits finis',1],
            [11, 'Chambre froide réception',1],
            [12, 'Congélateur',1],
            [13, 'Découpe',1],
            [14, 'Etal poissonnerie',1],
            [15, 'Frigo',1],
            [16, 'Frigo découpe',1],
            [17, 'Frigo dynamique',1],
            [18, 'Frigo expéditions',1],
            [19, 'Frigo mécanisé',1],
            [20, 'Frigo stockage',1],
            [21, 'Hall de conditionnement hors Fontenoy',1],
            [22, 'Liaison chaude',1],
            [23, 'Liaison froide',1],
            [24, 'Point chaud',1],
            [25, 'Préparation chaude',1],
            [26, 'Préparation froide',1],
            [27, 'Salle découpe',1],
            [28, 'Self',1],
            [29, 'Self chaud',1],
            [30, 'Self froid',1],
            [31, 'Stock nu',1],
            [32, 'Stock picking',1],
            [33, 'Vitrine réfrigérée',1],
            [34, 'Vitrine réfrigérée LS',1],
            [35, 'Vitrine réfrigérée self',1],
            [36, 'VR Boucherie traditionnelle',1],
            [37, 'VR Charcuterie traditionnelle',1],
            [38, 'VR Fromagerie traditionnelle',1],
            [39, 'VR Pâtisserie traditionnelle',1],
            [40, 'VR Traiteur traditionnelle',1],
            [41, 'VR LS Boucherie',1],
            [42, 'VR LS Charcuterie',1],
            [43, 'VR LS Fromagerie',1],
            [44, 'VR LS Pâtisserie',1],
            [45, 'VR LS  Poissonnerie',1],
            [46, 'VR LS  Traiteur',1],
            [47, 'Tour réfrigéré',1]
        ]);
    }

    public function down()
    {
        echo "m160408_074848_insert_analyse_lieu_prelevement cannot be reverted.\n";

        return false;
    }
}