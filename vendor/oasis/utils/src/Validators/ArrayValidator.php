<?php
/**
 * Created by PhpStorm.
 * User: minhao
 * Date: 2016-09-02
 * Time: 17:29
 */

namespace Oasis\Mlib\Utils\Validators;

use Oasis\Mlib\Utils\Exceptions\InvalidArrayElementException;
use Oasis\Mlib\Utils\Exceptions\InvalidDataTypeException;

class ArrayValidator implements ValidatorInterface
{
    /** @var  whether null is considered valid or not */
    protected $allowNull;
    /** @var ValidatorInterface */
    protected $elementValidator;
    
    public function __construct($allowNull = false, $elementValidator = null)
    {
        $this->allowNull = $allowNull;
        if ($elementValidator == null) {
            $elementValidator = new DummyValidator();
        }
        $this->elementValidator = $elementValidator;
    }
    
    public function validate($target)
    {
        if (is_null($target) && $this->allowNull) {
            return [];
        }
        if (!is_array($target)) {
            throw new InvalidDataTypeException("Target is not an array!");
        }
        
        $result = [];
        foreach ($target as $k => $item) {
            try {
                $result[$k] = $this->elementValidator->validate($item);
            } catch (InvalidDataTypeException $e) {
                throw new InvalidArrayElementException(
                    sprintf(
                        'Invalid element in array for index %s, reason = %s',
                        $k,
                        $e->getMessage()
                    )
                );
            }
        }
        
        return $result;
    }
}
