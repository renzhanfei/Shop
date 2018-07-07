<?php
/**
 * Created by SlimApp.
 *
 * Date: 2018-07-07
 * Time: 18:40
 */
use Oasis\Mlib\ODM\Dynamodb\Console\ConsoleHelper;
use Xinhai\Shop\Database\ShopDatabase;

require_once __DIR__ . '/../bootstrap.php';

$itemManager = ShopDatabase::getItemManager();

return new ConsoleHelper($itemManager);

