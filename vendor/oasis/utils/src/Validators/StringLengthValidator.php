<?php
/**
 * Created by PhpStorm.
 * User: minhao
 * Date: 2016-09-02
 * Time: 21:24
 */

namespace Oasis\Mlib\Utils\Validators;

use Oasis\Mlib\Utils\Exceptions\InvalidDataTypeException;
use Oasis\Mlib\Utils\Exceptions\StringTooLongException;
use Oasis\Mlib\Utils\Exceptions\StringTooShortException;
use voku\helper\UTF8;

class StringLengthValidator implements ValidatorInterface
{
    protected $maxLength = 0;
    protected $minLength = 0;
    protected $chopDown  = false;
    protected $encoding  = 'UTF-8';
    
    public function __construct($maxLength, $minLength = 0, $chopDown = false, $encoding = "UTF-8")
    {
        $this->maxLength = $maxLength;
        $this->minLength = $minLength;
        $this->chopDown  = $chopDown;
        $this->encoding  = $encoding;
    }
    
    public function validate($target)
    {
        if (!is_string($target)) {
            throw new InvalidDataTypeException(
                "Target is not a string, and cannot be validated according to string length!"
            );
        }
        
        $len = UTF8::strlen($target, $this->encoding);
        
        if ($len < $this->minLength) {
            throw new StringTooShortException("Length of target is too short, min = " . $this->minLength);
        }
        elseif ($len > $this->maxLength) {
            if (!$this->chopDown) {
                throw new StringTooLongException("Length of target is too long, max = " . $this->maxLength);
            }
            else {
                $target = UTF8::substr($target, 0, $this->maxLength, $this->encoding);
            }
        }
        
        return $target;
        
    }
}
