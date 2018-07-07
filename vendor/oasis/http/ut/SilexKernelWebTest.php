<?php
use Oasis\Mlib\Http\SilexKernel;
use Oasis\Mlib\Http\Views\JsonViewHandler;
use Oasis\Mlib\Utils\StringUtils;
use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Created by PhpStorm.
 * User: minhao
 * Date: 2016-03-08
 * Time: 11:03
 */
class SilexKernelWebTest extends WebTestCase
{
    
    /**
     * Creates the application.
     *
     * @return HttpKernelInterface
     */
    public function createApplication()
    {
        /** @var SilexKernel $app */
        $app = require __DIR__ . '/app.php';
        $app->addExtraParameters(
            [
                'app.config1' => 'one',
                'app.config2' => 'two',
            ]
        );
        
        return $app;
    }
    
    public function testHomeRoute()
    {
        $client = $this->createClient();
        $client->request('GET', '/');
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals('Oasis\\Mlib\\Http\\Test\\Helpers\\Controllers\\TestController::home()', $json['called']);
    }
    
    public function testHttpOnlyRoute()
    {
        $client = $this->createClient();
        $client->request('GET', '/httponly', [], [], ['HTTPS' => 'on']);
        /** @var RedirectResponse $response */
        $response = $client->getResponse();
        $this->assertTrue($response instanceof RedirectResponse);
        $this->assertTrue(StringUtils::stringStartsWith($response->getTargetUrl(), 'http://'));
    }
    
    public function testNotFoundRoute()
    {
        $client = $this->createClient();
        $client->request('GET', '/404'); // non existing route
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertTrue(isset($json['code']), $response->getContent());
    }
    
    public function testHostBasedRoutes()
    {
        $client = $this->createClient(['HTTP_HOST' => 'localhost']);
        $client->request('GET', '/domain');
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals(
            'Oasis\\Mlib\\Http\\Test\\Helpers\\Controllers\\TestController::domainLocalhost()',
            $json['called']
        );
        
        $client = $this->createClient(['HTTP_HOST' => 'baidu.com']);
        $client->request('GET', '/domain');
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals(
            'Oasis\\Mlib\\Http\\Test\\Helpers\\Controllers\\TestController::domainBaidu()',
            $json['called']
        );
    }
    
    public function testSubRoutes()
    {
        $client = $this->createClient();
        $client->request('GET', '/sub/');
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals('mama', $json['attributes']['name']);
        $this->assertEquals('Oasis\\Mlib\\Http\\Test\\Helpers\\Controllers\\SubTestController::sub()', $json['called']);
    }
    
    public function testDomainMatching()
    {
        $client = $this->createClient(['HTTP_HOST' => "naruto.baidu.com"]);
        $client->request('GET', '/param/domain');
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals(
            'Oasis\\Mlib\\Http\\Test\\Helpers\\Controllers\\TestController::paramDomain()',
            $json['called']
        );
        $this->assertEquals('naruto', $json['game']);
        
    }
    
    public function testParameterFromConfig()
    {
        $client = $this->createClient(['HTTP_HOST' => "naruto.baidu.com"]);
        $client->request('GET', '/param/config-value');
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals(
            'Oasis\\Mlib\\Http\\Test\\Helpers\\Controllers\\TestController::paramConfigValue()',
            $json['called']
        );
        $this->assertEquals('one', $json['one'], $response->getContent());
        $this->assertEquals('two', $json['two']);
        $this->assertEquals('onetwo', $json['three']);
    }
    
    public function testParameterMatching()
    {
        $client = $this->createClient(['HTTP_HOST' => "naruto.baidu.com"]);
        $client->request('GET', '/param/id/29');
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals(
            'Oasis\\Mlib\\Http\\Test\\Helpers\\Controllers\\TestController::paramId()',
            $json['called']
        );
        $this->assertEquals('29', $json['id']);
        
        $client = $this->createClient(['HTTP_HOST' => "naruto.baidu.com"]);
        $client->request('GET', '/param/id/moi');
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals(
            'Oasis\\Mlib\\Http\\Test\\Helpers\\Controllers\\TestController::paramSlug()',
            $json['called']
        );
        $this->assertEquals('moi', $json['slug']);
        
        $client = $this->createClient(['HTTP_HOST' => "naruto.baidu.com"]);
        $client->request('GET', '/param/id/moi/hei');
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals(
            'Oasis\\Mlib\\Http\\Test\\Helpers\\Controllers\\TestController::paramSlug()',
            $json['called']
        );
        $this->assertEquals('moi/hei', $json['slug']);
        
    }
    
    public function testParameterRetrieval()
    {
        $client = $this->createClient();
        $client->request(
            'GET',
            '/param/chained/30',
            [
                'name' => 'John',
                'age'  => 80,
            ]
        );
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals(
            'Oasis\\Mlib\\Http\\Test\\Helpers\\Controllers\\TestController::paramChained()',
            $json['called']
        );
        $this->assertEquals('30', $json['id']);
        $this->assertEquals('John', $json['name']);
        $this->assertEquals(80, $json['age']);
        $this->assertEquals(999.99, $json['salary']);
        
        $client->request(
            'POST',
            '/param/chained/30?id=9&name=Ali',
            [
                'name' => 'John',
                'age'  => 80,
            ]
        );
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals('30', $json['id']);
        $this->assertEquals('Ali', $json['name']);
        $this->assertEquals(80, $json['age']);
        $this->assertEquals(999.99, $json['salary']);
        
    }
    
    public function testInjectedArg()
    {
        $client = $this->createClient(['HTTP_HOST' => "naruto.baidu.com"]);
        
        $client->request('GET', '/param/injected');
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals(
            'Oasis\\Mlib\\Http\\Test\\Helpers\\Controllers\\TestController::paramInjected()',
            $json['called']
        );
        $this->assertEquals(JsonViewHandler::class, $json['handler']);
        
        $client->request('GET', '/param/injected2');
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals(
            'Oasis\\Mlib\\Http\\Test\\Helpers\\Controllers\\TestController::paramInjectedWithInheritedClass()',
            $json['called']
        );
        $this->assertEquals(JsonViewHandler::class, $json['handler']);
    }
    
    public function testTrustedProxies()
    {
        $client    = $this->createClient();
        $forwarded = "1.2.3.5";
        $client->request('GET', '/proxy/test', [], [], ["HTTP_X_FORWARDED_FOR" => $forwarded]);
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals('1.2.3.5', $json['from'], $response->getContent());
        
        $forwarded = "1.2.3.5, 1.2.3.4";
        $client->request('GET', '/proxy/test', [], [], ["HTTP_X_FORWARDED_FOR" => $forwarded]);
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals('1.2.3.5', $json['from'], $response->getContent());
        $forwarded = "1.2.3.4, 1.2.3.5";
        $client->request('GET', '/proxy/test', [], [], ["HTTP_X_FORWARDED_FOR" => $forwarded]);
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals('1.2.3.5', $json['from'], $response->getContent());
        $forwarded = "1.2.3.3, 1.2.3.4, 1.2.3.5";
        $client->request('GET', '/proxy/test', [], [], ["HTTP_X_FORWARDED_FOR" => $forwarded]);
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals('1.2.3.5', $json['from'], $response->getContent());
        $forwarded = "1.2.3.2, 1.2.3.3, 1.2.3.4";
        $client->request('GET', '/proxy/test', [], [], ["HTTP_X_FORWARDED_FOR" => $forwarded]);
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals('1.2.3.3', $json['from'], $response->getContent());
        
        $forwarded = "1.2.3.2, 1.2.3.3, 5.6.7.8";
        $client->request('GET', '/proxy/test', [], [], ["HTTP_X_FORWARDED_FOR" => $forwarded]);
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals('1.2.3.3', $json['from'], $response->getContent());
        $forwarded = "1.2.3.2, 1.2.3.3, 5.6.7.88";
        $client->request('GET', '/proxy/test', [], [], ["HTTP_X_FORWARDED_FOR" => $forwarded]);
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals('1.2.3.3', $json['from'], $response->getContent());
        $forwarded = "1.2.3.2, 1.2.3.3, 5.6.77.88";
        $client->request('GET', '/proxy/test', [], [], ["HTTP_X_FORWARDED_FOR" => $forwarded]);
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals('1.2.3.3', $json['from'], $response->getContent());
        $forwarded = "1.2.3.2, 1.2.3.3, 5.66.77.88";
        $client->request('GET', '/proxy/test', [], [], ["HTTP_X_FORWARDED_FOR" => $forwarded]);
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals('5.66.77.88', $json['from'], $response->getContent());
    }
    
    public function testCookieContainer()
    {
        $client = $this->createClient();
        $client->request('GET', '/cookie/set');
        $response = $client->getResponse();
        $client->request('GET', '/cookie/check');
        $response = $client->getResponse();
        $json     = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($json));
        $this->assertEquals(
            'Oasis\\Mlib\\Http\\Test\\Helpers\\Controllers\\TestController::cookieChecker()',
            $json['called']
        );
        $this->assertEquals('John', $json['name']);
        
    }
}
