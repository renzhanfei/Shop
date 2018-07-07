<?php
/**
 * Created by SlimApp.
 *
 * Date: 2018-07-07
 * Time: 18:41
 */

namespace Xinhai\Shop\Controllers;

use Symfony\Component\HttpFoundation\Response;

class DemoController
{
    public function testAction()
    {
        return new Response('Hello World!');
    }
}

