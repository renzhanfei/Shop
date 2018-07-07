<?php
/**
 * Created by PhpStorm.
 * User: minhao
 * Date: 2016-01-19
 * Time: 21:00
 */

namespace Oasis\Mlib\Utils;

class CommonUtils
{
    public static function isRunningFromCommandLine()
    {
        static $isCli = null;
        if ($isCli === null) {
            $isCli = (
                !isset($_SERVER['SERVER_SOFTWARE'])
                && (
                    php_sapi_name() == 'cli'
                    || (is_numeric($_SERVER['argc']) && $_SERVER['argc'] > 0)
                )
            );
        }
        
        return $isCli;
    }
    
    public static function monitorMemoryUsage($minUsage = 128000000,
                                              $lowerThreshold = 10,
                                              $upperThreshold = 70
    )
    {
        static $isLowest = false;
        static $neverReset = true;
        
        $currentUsage = memory_get_usage();
        $currentLimit = ini_get('memory_limit');
        $last         = strtolower($currentLimit[strlen($currentLimit) - 1]);
        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $currentLimit = substr($currentLimit, 0, (strlen($currentLimit) - 1));
                $currentLimit *= 1024 * 1024 * 1024;
                break;
            case 'm':
                $currentLimit = substr($currentLimit, 0, (strlen($currentLimit) - 1));
                $currentLimit *= 1024 * 1024;
                break;
            case 'k':
                $currentLimit = substr($currentLimit, 0, (strlen($currentLimit) - 1));
                $currentLimit *= 1024;
                break;
        }
        $newLimit        = $currentLimit;
        $usagePercentage = $currentUsage / $currentLimit * 100;
        $resetNeeded     = false;
        if ($usagePercentage > $upperThreshold) {
            $newLimit    = $currentUsage * 100 / (($upperThreshold + $lowerThreshold) / 2);
            $isLowest    = false;
            $resetNeeded = true;
        }
        else if ($usagePercentage < $lowerThreshold && !$neverReset && !$isLowest) {
            $newLimit = $currentUsage * 100 / (($upperThreshold + $lowerThreshold) / 2);
            if ($newLimit < $minUsage) {
                $newLimit = $minUsage;
                $isLowest = true;
            }
            $resetNeeded = true;
        }
        
        if ($resetNeeded) {
            $unit = "";
            if ($newLimit > 1024) {
                $newLimit = ceil($newLimit / 1024);
                $unit     = 'K';
            }
            if ($newLimit > 1024) {
                $newLimit = ceil($newLimit / 1024 * 100) / 100;
                $unit     = 'M';
            }
            if ($newLimit > 1024) {
                $newLimit = ceil($newLimit / 1024 * 100) / 100;
                $unit     = 'G';
            }
            $newLimit = $newLimit . $unit;
            ini_set('memory_limit', $newLimit);
            if (self::isRunningFromCommandLine()) {
                fprintf(
                    STDERR,
                    "memory limit adjusted dynamically - $newLimit (from $currentLimit), cur = $currentUsage\n"
                );
            }
            $neverReset = false;
        }
    }
    
    public static function registerMemoryMonitorForTick()
    {
        $function_name = __CLASS__ . "::monitorMemoryUsage";
        register_tick_function($function_name);
    }
    
    /**
     *
     * makes an unsigned shift of an integer given bits
     *
     * @param int $num
     * @param int $bits
     *
     * @return int
     */
    public static function unsignedRightShift($num, $bits)
    {
        if ($bits == 0) {
            return $num;
        }
        
        return ($num >> $bits) & ~(1 << (8 * PHP_INT_SIZE - 1) >> ($bits - 1));
    }
}
