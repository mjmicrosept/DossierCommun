<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 17/10/2018
 * Time: 14:48
 */

namespace app\assets\views;

namespace app\assets\views;
use yii\web\AssetBundle;


class KartikCommonAsset extends AssetBundle
{
    public $sourcePath = '@vendor/kartik-v';
    public $css = [

    ];
    public $js = [
        'bootstrap-fileinput/js/locales/fr.js'
    ];
    public $depends = [
    ];
}