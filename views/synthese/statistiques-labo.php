<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 30/01/2019
 * Time: 10:33
 */

use kartik\builder\FormAsset;
use app\assets\views\KartikCommonAsset;
use app\assets\components\SweetAlert\SweetAlertAsset;

$this->title = Yii::t('microsept', 'Stat Labo');
$this->params['breadcrumbs'][] = Yii::t('microsept', 'Stat Labo');

$baseUrl = Yii::$app->request->baseUrl;
FormAsset::register($this);
KartikCommonAsset::register($this);
SweetAlertAsset::register($this);

$this->registerCss(<<<CSS
    .nav > li.disabled > a {
        color: #C7C7C7;
    }
    .box-header{
        padding:10px 15px;
    }
    .box{
        border-radius:5px;
        border-top:1px solid transparent;
    }
    .btn-box-tool{
        color:#FFF;
    }
    .btn-box-tool:hover{
        color:#FFF;
    }
    .btn-box-tool:focus{
        color:#FFF;
    }
CSS
);

?>

<div class="loader">
    <div class="sk-cube-grid"><div class="sk-cube sk-cube1"></div>
        <div class="sk-cube sk-cube2"></div><div class="sk-cube sk-cube3"></div>
        <div class="sk-cube sk-cube4"></div><div class="sk-cube sk-cube5"></div>
        <div class="sk-cube sk-cube6"></div><div class="sk-cube sk-cube7"></div>
        <div class="sk-cube sk-cube8"></div><div class="sk-cube sk-cube9"></div>
        <div class="loader-traitement">Traitement en cours</div>
    </div>
</div>
<h2 class="lte-hide-title"><?= $this->title ?></h2>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary box">
            <div class="panel-heading box-header">
                <strong>
                    <span class="fas fa-search"></span> <?= Yii::t('microsept', 'Search') ?>
                </strong>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="panel-body box-body">

                <button class="btn btn-primary btn-see-results" style="float:right;margin:20px 0 10px 0;"><i class="far fa-eye"></i> Voir les résultats</button>
            </div>
        </div>
    </div>
</div>
