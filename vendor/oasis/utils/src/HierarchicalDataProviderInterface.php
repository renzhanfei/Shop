<?php
/**
 * Created by PhpStorm.
 * User: minhao
 * Date: 2016-02-17
 * Time: 21:03
 */

namespace Oasis\Mlib\Utils;

interface HierarchicalDataProviderInterface extends DataProviderInterface
{
    public function getCurrentPath();

    public function setCurrentPath($path);

    public function pushPath($relativePath);

    public function popPath();

    public function getPathDelimiter();

    public function setPathDelimiter($cascade_delimiter);
}
