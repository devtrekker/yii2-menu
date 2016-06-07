Bootstrap Menu Manager for Yii2
================================

[![Latest Stable Version](https://poser.pugx.org/pceuropa/yii2-menu/v/stable)](https://packagist.org/packages/pceuropa/yii2-menu) [![Total Downloads](https://poser.pugx.org/pceuropa/yii2-menu/downloads)](https://packagist.org/packages/pceuropa/yii2-menu) [![Latest Unstable Version](https://poser.pugx.org/pceuropa/yii2-menu/v/unstable)](https://packagist.org/packages/pceuropa/yii2-menu) [![License](https://poser.pugx.org/pceuropa/yii2-menu/license)](https://packagist.org/packages/pceuropa/yii2-menu)
![preview](http://pceuropa.net/imgs/yii2-menu2.png)

[DEMO](http://yii2-menu.pceuropa.net/menu)

## Features

 * Creating links, drop menus, line (divider) in the navbar-left and/or navbar-right.
 * Sorting, editing, and deleting using drag and drop.
 * No jQuery for drag and drop ([RubaXa/Sortable](https://github.com/RubaXa/Sortable)).
 * CRUD operations by jQuery Ajax.
 
## Installation
```
composer require devtrekker/yii2-menu dev-master
```

Add the following code to config file Yii2
```php
'modules' => [
	'menu' => [
            'class' => '\devtrekker\menu\Module',
        ],
	]
```

## Configuration

### 1. Create database schema

Make sure that you have properly configured the `db` application component,
and run the following command:

```bash
$ php yii migrate/up --migrationPath=@vendor/devtrekker/yii2-menu/migrations

```


### 2. Add the following code to config file Yii2

```php
$menu = new devtrekker\menu\Module([]);

NavBar::begin(['brandLabel' => 'Brand','brandUrl' => Url::home(),]);

echo Nav::widget([ 'options' => ['class' => 'navbar-nav navbar-left'],
					'items' => $menu->NavbarLeft() 
				]);	
					
echo Nav::widget([ 'options' => ['class' => 'navbar-nav navbar-right'],
					'items' => $menu->NavbarRight()
				]);
NavBar::end();

```

Alternatively, if you want to merge a static menu with this dynamic menu:
```php
$menu = new devtrekker\menu\Module([]);

NavBar::begin([
    //'brandLabel' => 'Acme, Inc.',
    //'brandLabel' => Yii::$app->params['companyNameShort'],
    //'brandLabel' => '<img src="'.Yii::$app->homeUrl.'img/frontpage/logo.png" style="margin: -10px; height: 40px;" valign="left">',
    //'brandUrl'   => Yii::$app->homeUrl,
    'options' => [
        //'class' => 'navbar navbar-inverse navbar-fixed-top',  // dark theme
        //'class' => 'navbar navbar-default',                   // light theme
        'class' => 'navbar navbar-inverse',                     // dark theme
    ],
    //'submenuOptions' => ['target' => '_blank'],  // to go blank tab for each menu item
]);
$menuItems = [
    ['label' => 'Home', 'url' => ['/site/index']],
    ...
];    
                
// Merge available menu entries into a single menu
//$menuItems = (array_slice($menuItems, 0, 1, true) + $menu->NavbarRight() + array_slice($menuItems, 1, count($menuItems)-1, true));  // insert in a specific index, using array union (duplicate items with numeric keys in 2nd array are ignored)
//$menuItems = (array_slice($menuItems, 0, 1, true) + $menu->NavbarRight() + $menuItems);  // insert in a specific index, using array union (duplicate items with numeric keys in 2nd array are ignored)
//$menuItems = array_merge($menu->NavbarRight(), $menuItems);  // prepend, using array merge (duplicate items with numeric keys get appended)
$menuItems   = ($menu->NavbarRight() + $menuItems);            // prepend, using array union (duplicate items with numeric keys in 2nd array are ignored)

echo Nav::widget([ 
    'options' => ['class' => 'navbar-inverse navbar-nav navbar-left'],
    'items' => $menu->NavbarLeft() 
]);	

echo Nav::widget([ 
    'options' => ['class' => 'navbar-inverse navbar-nav navbar-right'],
    'encodeLabels' => false,  // to allow icons in labels
    //'items' => $menu->NavbarRight()
    'items' => $menuItems //$menu->NavbarRight()
]);

NavBar::end();
```

### 3. Several menus
If you need a second menu:
You need to create a second database (eg. menu_fr), and add references to it. Find this code in devtrekker/models/Model.php:
```php
public static function tableName() 
{ 
	return 'menu'; 
}
```
and change for it

```php
public static function tableName() 
{ 
	if Yii:$app->language == "en" {
		return 'menu'; 
	} else {
	 	return "menu_fr"
	}
}
```
Author: [@Marguzewicz](https://twitter.com/Marguzewicz) | [Donation](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=patriota%40or7%2eeu&lc=PL&item_name=Rafal%20Marguzewicz&no_note=1&no_shipping=1&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted)
