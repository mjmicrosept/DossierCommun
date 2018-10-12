<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 11/10/2018
 * Time: 16:00
 */

namespace app\assets\components\SweetAlert;
use yii\web\AssetBundle;

class SweetAlertAsset extends AssetBundle
{

    /**
     * @var string the directory that contains the source asset files for this asset bundle.
     * A source asset file is a file that is part of your source code repository of your Web application.
     */
    public $sourcePath = '@bower/sweetalert2/dist';

    /**
     * @var array list of JavaScript files that this bundle contains. Each JavaScript file can be
     * specified in one of the following formats:
     */
    public $js = [
        'sweetalert2.min.js',
    ];

    /**
     * @var array list of CSS files that this bundle contains. Each CSS file can be specified
     * in one of the three formats as explained in [[js]].
     */
    public $css = [
        'sweetalert2.css'
    ];

}
{

}