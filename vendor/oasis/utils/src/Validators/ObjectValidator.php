<?php
/**
 * Created by PhpStorm.
 * User: minhao
 * Date: 2016-09-02
 * Time: 17:29
 */

namespace Oasis\Mlib\Utils\Validators;

use Oasis\Mlib\Utils\Exceptions\InvalidDataTypeException;

class ObjectValidator implements ValidatorInterface
{
    /** @var  whether null is considered valid or not */
    protected $allowNull;
    
    public function __construct($allowNull = true)
    {
        $this->allowNull = $allowNull;
    }
    
    public function validate($target)
    {
        if (is_null($target) && $this->allowNull) {
            return null;
        }
        
        if (!is_object($target)) {
            throw new InvalidDataTypeException("Validated data is not an object!");
        }
        
        return $target;
    }
}
