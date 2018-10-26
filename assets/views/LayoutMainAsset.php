<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 05/10/2018
 * Time: 09:49
 */

namespace app\assets\views;
use yii\web\AssetBundle;
/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class LayoutMainAsset extends AssetBundle
{

    public $sourcePath = '@app/assets/web';
    public $css = [
        'css/site.css',
        'css/animate.min.css',
        'css/all-skin.css',
        'css/loader.css',
        'css/404.css',
        'css/bootstrap-toggle.css',
    ];
    public $js = [
        'js/layouts.main.js',
        'js/bootstrap-toggle.js',
    ];
    public $depends = [
        'app\assets\AppAsset',
        'app\assets\views\PluginMainAsset',
        'app\assets\views\BowerComponentsAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}