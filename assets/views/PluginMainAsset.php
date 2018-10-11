<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 05/10/2018
 * Time: 09:51
 */

namespace app\assets\views;
use yii\web\AssetBundle;
/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class PluginMainAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins';
    public $css = [
        'pace/pace.min.css'
    ];
    public $js = [
        'pace/pace.min.js',
    ];
    public $depends = [
    ];
}