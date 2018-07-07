<?php
/**
 * Created by SlimApp.
 *
 * Date: 2018-07-07
 * Time: 18:41
 */


use Xinhai\Shop\Shop;

/** @var Shop $app */
$app = require_once __DIR__ . "/../bootstrap.php";

$app->getHttpKernel()->run();

