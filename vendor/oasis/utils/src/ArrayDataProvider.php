<?php
/**
 * Created by PhpStorm.
 * User: minhao
 * Date: 2015-09-15
 * Time: 11:45
 */
namespace Oasis\Mlib\Utils;

class ArrayDataProvider extends AbstractDataProvider
    implements HierarchicalDataProviderInterface
{
    protected $data      = [];
    protected $delimeter = ".";
    protected $paths     = [];

    function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    protected function getValue($key)
    {
        return $this->getRealValue($key, true);
    }

    protected function getRealValue($key, $isRelative = false)
    {
        $data = $this->data;
        if ($isRelative && $this->paths) {
            $data = $this->getRealValue(implode($this->delimeter, $this->paths));
            if (!is_array($data)) {
                return null;
            }
        }

        $parts     = explode($this->delimeter, $key);
        $branchKey = '';
        while (sizeof($parts) > 0) {
            $currentKey = implode($this->delimeter, $parts);
            if ($branchKey == '' && array_key_exists($currentKey, $data)) {
                return $data[$currentKey];
            }
            $branchKey .= (strlen($branchKey) > 0 ? '.' : '') . $parts[0];
            array_shift($parts);
            if (array_key_exists($branchKey, $data) && is_array($data[$branchKey])) {
                $data      = &$data[$branchKey];
                $branchKey = '';
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getPathDelimiter()
    {
        return $this->delimeter;
    }

    /**
     * @param string $delimeter
     */
    public function setPathDelimiter($delimeter)
    {
        if (strlen($delimeter) != 1) {
            throw new \InvalidArgumentException(
                "Cascade delimiter should be a single character. given = " . $delimeter
            );
        }
        $this->delimeter = $delimeter;
    }

    public function getCurrentPath()
    {
        return implode($this->delimeter, $this->paths);
    }

    public function setCurrentPath($path)
    {
        if (!$path) {
            $this->paths = [];
        }
        else {
            $this->paths = explode($this->delimeter, $path);
        }

    }

    public function pushPath($relativePath)
    {
        $parts       = explode($this->delimeter, $relativePath);
        $this->paths = array_merge($this->paths, $parts);
    }

    public function popPath()
    {
        if (sizeof($this->paths) > 0) {
            array_pop($this->paths);
        }
    }
}
