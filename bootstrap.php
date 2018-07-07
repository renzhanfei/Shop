<?php
/**
 * Created by SlimApp.
 *
 * Date: 2018-07-07
 * Time: 18:41
 */

use Xinhai\Shop\Shop;
use Xinhai\Shop\ShopConfiguration;

require_once __DIR__ . "/vendor/autoload.php";

define('PROJECT_DIR', __DIR__);

/** @var Shop $app */
$app = Shop::app();
$app->init(__DIR__ . "/config", new ShopConfiguration(), __DIR__ . "/cache/config");

return $app;

