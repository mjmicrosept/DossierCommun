<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 05/12/2018
 * Time: 12:17
 */

namespace app\assets\components\ChartJs;

use yii\web\AssetBundle;


class ChartJsAsset extends AssetBundle
{
    /**
     * @var string the directory that contains the source asset files for this asset bundle.
     * A source asset file is a file that is part of your source code repository of your Web application.
     */
    public $sourcePath = '@bower/chartjs/dist';

    /**
     * @var array list of JavaScript files that this bundle contains. Each JavaScript file can be
     * specified in one of the following formats:
     */
    public $js = [
        'Chart.js',
        'Chart.bundle.js',
    ];

    /**
     * @var array list of CSS files that this bundle contains. Each CSS file can be specified
     * in one of the three formats as explained in [[js]].
     */
    public $css = [

    ];
}