<?php

namespace Contao\LegacyBundle\Tests\Module\Core\Library;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Contao\LegacyBundle\Module\Core\Library\Environment;

class EnvironmentTest extends WebTestCase
{

    protected $request;

    public function setUp()
    {
        $this->request = new Request;
        static::createClient()->getKernel()->getContainer()->get('request_stack')->push($this->request);

        foreach ($this->request->server->keys() as $key) {
            $this->request->server->remove($key);
        }
    }

    public function initializeRequestWithServer($server)
    {
        $this->request->initialize(array(), array(), array(), array(), array(), $server);
    }


    /**
     * TESTS
     */

    public function testHttpHost()
    {
        $this->initializeRequestWithServer(array(
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => 80
        ));
        $this->assertEquals('localhost', \Environment::get('httpHost'));

        $this->initializeRequestWithServer(array(
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => 443
        ));
        $this->assertEquals('localhost:443', \Environment::get('httpHost'));

        $this->initializeRequestWithServer(array(
            'HTTP_HOST' => 'example.com'
        ));
        $this->assertEquals('example.com', \Environment::get('httpHost'));
    }

    public function testIsAjaxRequest()
    {
        $this->assertFalse(\Environment::get('isAjaxRequest'));

        $this->initializeRequestWithServer(array(
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ));

        $this->assertTrue(\Environment::get('isAjaxRequest'));
    }
}
