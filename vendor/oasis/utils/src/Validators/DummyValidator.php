<?php
/**
 * Created by PhpStorm.
 * User: minhao
 * Date: 2016-09-02
 * Time: 17:39
 */

namespace Oasis\Mlib\Utils\Validators;

class DummyValidator implements ValidatorInterface
{
    
    public function validate($target)
    {
        return $target;
    }
}
