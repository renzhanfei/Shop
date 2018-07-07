<?php
/**
 * Created by SlimApp.
 *
 * Date: 2018-07-07
 * Time: 18:40
 */
 
 
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Xinhai\Shop\Database\ShopDatabase;

require_once __DIR__ . "/../bootstrap.php";

return ConsoleRunner::createHelperSet(ShopDatabase::getEntityManager());
