<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 05/10/2018
 * Time: 15:38
 */

namespace app\assets\views;
use yii\web\AssetBundle;


class BowerComponentsAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/bower_components';
    public $css = [

    ];
    public $js = [
        'jquery-slimscroll/jquery.slimscroll.min.js'
    ];
    public $depends = [
    ];
}