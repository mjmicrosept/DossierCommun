<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 15/11/2018
 * Time: 09:04
 */

use yii\helpers\Url;
use app\models\AnalyseConditionnement;
use app\models\AnalyseLieuPrelevement;
//Yii::trace($germes);
//Yii::trace($prelevements);

?>
<div class="panel panel-default" style="margin-top:10px;">
    <div class="panel-heading">Liste des germes</div>
    <table class="table">
        <thead class="thead-dark">
        <th>Conditionnement</th>
        <th>Lieu de prélèvement</th>
        </thead>
        <tbody>
        <?php
            $result = '';
            $result .= '<tr>';
            $result .= '<td>';
            $conditionnement = '-';
            if(!is_null($prelevements['id_conditionnement']))
                $conditionnement = AnalyseConditionnement::find()->andFilterWhere(['id'=>$prelevements['id_conditionnement']])->one()->libelle;
            $result .= $conditionnement;
            $result .= '</td>';
            $result .= '<td>';
            $lieuPrelevement = '-';
            if(!is_null($prelevements['id_lieu_prelevement']))
                $lieuPrelevement = AnalyseLieuPrelevement::find()->andFilterWhere(['id'=>$prelevements['id_lieu_prelevement']])->one()->libelle;
            $result .= $lieuPrelevement;
            $result .= '</td>';
            $result .= '</tr>';

            echo $result;
        ?>
        </tbody>
    </table>
</div>

<div class="panel panel-default">
    <div class="panel-heading">Liste des germes</div>
    <table class="table">
        <thead class="thead-dark">
        <th>Germe</th>
        <th>Résultat</th>
        <th>Expression</th>
        <th>Interprétation</th>
        </thead>
        <tbody>
        <?php
        foreach ($germes as $item) {
            $result = '';
            $result .= '<tr>';
            $result .= '<td>';
            $result .= $item['libelle'];
            $result .= '</td>';
            $result .= '<td>';
            $result .= $item['resultat'];
            $result .= '</td>';
            $result .= '<td>';
            $result .= $item['expression'];
            $result .= '</td>';
            $result .= '<td>';
            $result .= $item['interpretation'];
            $result .= '</td>';
            $result .= '</tr>';

            echo $result;
        }
        ?>
        </tbody>
    </table>
</div>
