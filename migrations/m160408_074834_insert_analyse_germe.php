<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 05/11/2018
 * Time: 10:06
 */

use yii\db\Migration;

class m160408_074834_insert_analyse_germe extends Migration
{
    public function up()
    {
        $this->batchInsert('analyse_germe',['id', 'id_service','libelle','code','active'],[
            [1, 1, 'Aspergillus brasiliensis','Asp. brasi.',1],
            [2, 1, 'Aspergillus niger','ASPN',1],
            [3, 1, 'Bactéries aérobies mésophiles','BACT',1],
            [4, 1, 'Candida albicans','CA',1],
            [5, 1, 'Escherichia coli','EC',1],
            [6, 1, 'Flore totale','FT01',1],
            [7, 1, 'Germes non spécifiés','GNS',1],
            [8, 1, 'Germes spécifiés','GS',1],
            [9, 1, 'Germes totaux','GTTM103',1],
            [10, 1, 'Germes totaux','GTOREAL',1],
            [11, 1, 'Germes totaux Procter et Gamble','GTPG',1],
            [12, 1, 'Identification microbienne','ID',1],
            [13, 1, 'Levures-moisissures','LM',1],
            [15, 1, 'Levures-moisissures','LMTM103',1],
            [16, 1, 'Levures-moisissures à 20-25°C','LMOREAL',1],
            [17, 1, 'Levures moisissures Procter et Gamble','LMPG',1],
            [18, 1, 'Moisissures','Mois',1],
            [19, 1, 'Pseudomonas aeruginosa','PS',1],
            [20, 1, 'Staphylococcus aureus','STA',1],
            [21, 2, 'Bactéries lactiques mésophiles','FL01',1],
            [22, 2, 'Coliformes (UFC)','CTEAU',1],
            [23, 2, 'Coliformes thermotolérants','CFEAU',1],
            [24, 2, 'Dont Legionella pneumophila','LEGPN',1],
            [25, 2, 'Entérocoques intestinaux (UFC)','STREAU',1],
            [26, 2, 'Escherichia coli (UFC)','ECEAU',1],
            [27, 2, 'Legionella (confirmation du genre)','LEGIO',1],
            [28, 2, 'Legionella Pneumophila (identification de l\'espèce)','L_PNEUMO',1],
            [29, 2, 'Legionella spp','LEGSPP',1],
            [30, 2, 'Legionelle','LEG',1],
            [31, 2, 'Levures-moisissures','LMOGA',1],
            [32, 2, 'Listeria monocytogenes','LIST',1],
            [33, 2, 'Listeria spp','LSPP',1],
            [34, 2, 'Micro-organismes aérobies revivifiables à 22°C (UFC)','GTEAU22',1],
            [35, 2, 'Micro-organismes aérobies revivifiables à 30°C','GT30',1],
            [36, 2, 'Micro-organismes aérobies revivifiables à 36°C (UFC)','GTEAU37',1],
            [37, 2, 'Micro-organismes totaux à 30-35°C','GT30-35',1],
            [38, 2, 'Pseudomonas aeruginosa (UFC)','PSEAU',1],
            [39, 2, 'Pseudomonas spp','PSSPP',1],
            [40, 2, 'Salmonella','SALMEAU',1],
            [41, 2, 'Sérogroupe Legionella pneumophila','IDLP',1],
            [42, 2, 'Spore thermorésistantes mésophiles','SPORETH',1],
            [43, 2, 'Spores de micro-organismes anaérobies sulfito-réducteurs (UFC)','ASREAU',1],
            [44, 2, 'Staphylococcus à coagulase positive (UFC)','STAEAU',1],
            [45, 3, 'Bacillus cereus présomptif à 30°C','BCP',1],
            [46, 3, 'Bactéries lactiques mésophiles','BLM',1],
            [47, 3, 'Bactéries mésophiles aérobies (forme végétative)','BMA',1],
            [48, 3, 'Bactéries mésophiles anaérobies (forme végétative)','BMAna',1],
            [49, 3, 'Bactéries sulfito-réductrices à 37°c','ASR37',1],
            [50, 3, 'Bactéries sulfito-réductrices à 46°C','ASR',1],
            [51, 3, 'Campylobacter','CAMPY',1],
            [52, 3, 'Clostridium perfringens','CLOS',1],
            [53, 3, 'Coliformes (méthode JETRO)','C(JETRO)',1],
            [54, 3, 'Coliformes à 30°C','CT',1],
            [55, 3, 'Coliformes présumés à 30°C','CT',1],
            [56, 3, 'Coliformes présumés à 37°C','CT37',1],
            [57, 3, 'Coliformes thermotolérants','CF',1],
            [58, 3, 'Confirmation microbienne','CM',1],
            [59, 3, 'Entérobactéries','ENT',1],
            [60, 3, 'Enterobacteries à 37°c','ENT37ISO',1],
            [61, 3, 'Entérobactéries présumées à 30°C','ENT',1],
            [62, 3, 'Enterobactéries présumées à 37°C','ENT37',1],
            [63, 3, 'Enterococcus faecalis','ENTFAC',1],
            [64, 3, 'Enterococcus faecium','ENTFA',1],
            [65, 3, 'Entérocoques','STR',1],
            [66, 3, 'Escherichia coli B-glucuronidase positive','EC',1],
            [67, 3, 'Escherichia coli O157 incluant H7','ECO157',1],
            [68, 3, 'Levures','LEV',1],
            [69, 3, 'Levures et moisissures','LM',1],
            [70, 3, 'Levures et moisissures sur milieu à faible aw','LMAW',1],
            [71, 3, 'Levures sur milieu à faible aw','LAW',1],
            [72, 3, 'Listeria monocytogenes','LIST',1],
            [73, 3, 'Listeria spp','LSPP',1],
            [74, 3, 'Listeria spp (sauf L monocytogenes)','LIST SPP',1],
            [75, 3, 'Micro-organismes à 25°C','GT25',1],
            [76, 3, 'Micro-organismes à 30°C','GT30',1],
            [77, 3, 'Micro-organismes à 30°C anaérobies','GTANA30',1],
            [78, 3, 'Micro-organismes à 30°C incubés 48h','GT30S',1],
            [79, 3, 'Micro-organismes à 37°C','GT37',1],
            [80, 3, 'Micro-organismes psychrotrophes','GTSPYCHR',1],
            [81, 3, 'Microorganismes aérobies à 30°C + cycloheximide','GT30CYCLO',1],
            [82, 3, 'Moisissures','MS',1],
            [83, 3, 'Moisissures sur milieu à faible aw','MAW',1],
            [84, 3, 'Pseudomonas aeruginosa','PSAERU',1],
            [85, 3, 'Pseudomonas spp présomptifs','PS',1],
            [87, 3, 'Ratio micro-organismes à 30°C / bactéries lactiques','Ratio',1],
            [88, 3, 'Ratio micro-organismes à 30°C incubés à 48h/ bactéries lactiques','Ratio',1],
            [89, 3, 'Salmonella','SALM',1],
            [90, 3, 'Salmonella (sauf Typhi et Paratyphi)','SALM',1],
            [91, 3, 'Sérogroupes O26, O45, O103, O111, O121, O145 et O157','SRG STEC',1],
            [92, 3, 'Spores de Bacillus cereus présomptif à 30°C','SPBC',1],
            [93, 3, 'Spores de Bacillus et Clostridium à 30°C','SPO02',1],
            [94, 3, 'Spores de Bacillus et Clostridium à 55°C','SPO03',1],
            [95, 3, 'Spores de bactéries anaérobies à 30°C','SPO07',1],
            [96, 3, 'Spores de bactéries sulfito-réductrices à 37°C','SPASR37',1],
            [97, 3, 'Spores de bactéries sulfito-réductrices à 46°C','SPASR46',1],
            [98, 3, 'Spores thermorésistantes aérobies mésophiles','SPAM',1],
            [99, 3, 'Spores thermorésistantes aérobies thermophiles','SPAT',1],
            [100, 3, 'Spores thermorésistantes anaérobies mésophiles','SPANAM',1],
            [101, 3, 'Spores thermorésistantes anaérobies thermophiles','SPANAT',1],
            [102, 3, 'Spores thermorésistantes mésophiles','SPO01',1],
            [103, 3, 'Staphylococcus à coagulase positive à 37°C','STA',1],
            [104, 3, 'Streptococcus B hémolytique','STRB',1],
            [105, 3, 'Streptococcus thermophilus','STRTH',1],
            [106, 3, 'Streptococcus zooepidemicus','STRZOO',1],
            [107, 3, 'Streptocoques fécaux','STR',1],
            [108, 4, 'Bactéries','BACT',1],
            [109, 4, 'Bactéries Gram négatives résistantes aux sels biliaires','ENTPE',1],
            [110, 4, 'Candida albicans','CA',1],
            [111, 4, 'Champignons','CH',1],
            [112, 4, 'Charge microbienne totale','CMT',1],
            [113, 4, 'Dénombrement microbien','DM',1],
            [114, 4, 'Escherichia coli','ECPE',1],
            [115, 4, 'Germes aérobies totaux','DGAT',1],
            [116, 4, 'Germes aérobies totaux 34-38°c','GT',1],
            [117, 4, 'Germes aérobies totaux 40,5-42,5°c','GT',1],
            [118, 4, 'Germes aérobies totaux à 7 jours','DGAT7J',1],
            [119, 4, 'Levures','LPE',1],
            [120, 4, 'Levures Moisissures totales','DLMT',1],
            [121, 4, 'Levures Moisissures totales à 7 jours','DLMT7J',1],
            [122, 4, 'Micro-organismes aérobies viables','MAV',1],
            [123, 4, 'Moisissures','MPE',1],
            [124, 4, 'Pseudomonas aeruginosa','PSPE',1],
            [125, 4, 'Salmonella','SALMPE',1],
            [126, 4, 'Staphylococcus aureus','STAPE',1],
        ]);
    }

    public function down()
    {
        echo "m160411_101559_insert_analyse_germe cannot be reverted.\n";

        return false;
    }
}