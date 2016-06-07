<?php 
namespace devtrekker\menu;

use yii\web\AssetBundle;

class MenuAsset extends AssetBundle
{
    //public $sourcePath = __DIR__ . '/assets';                // this line generates error
    public $sourcePath = "@vendor/devtrekker/yii2-menu/assets";  // root directory that contains the asset files in this bundle
    
    public $baseUrl = '@web';
    public $js = [
        'js/Sortable.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}