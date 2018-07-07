<?php
/**
 * Created by PhpStorm.
 * User: minhao
 * Date: 2016-09-02
 * Time: 15:45
 */

namespace Oasis\Mlib\Utils\Validators;

use Oasis\Mlib\Utils\Exceptions\InvalidDataTypeException;

class IntegerValidator implements ValidatorInterface
{
    /** @var bool in non-strict mode, a string which can be parsed to integer is also considered integer */
    protected $strict = false;
    /** @var int base for integer parsing, only valid in non-strict mode */
    protected $base = 10;
    
    public function __construct($strict = false, $base = 10)
    {
        $this->strict = $strict;
        $this->base   = $base;
    }
    
    public function validate($target)
    {
        if (!$this->strict
            && (is_string($target) || is_float($target))
        ) {
            $intval = intval($target, $this->base);
            if (strval($intval) == strval($target)) {
                //echo(sprintf("%s equals %s\n", print_r($intval, true), print_r($target, true)));
                $target = $intval;
            }
        }
        
        if (!is_int($target)) {
            throw new InvalidDataTypeException("Validated data is not integer!");
        }
    
        return $target;
    }
}
