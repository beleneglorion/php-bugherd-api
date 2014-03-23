<?php

namespace Bugherd\Tests;

use Bugherd\Client;
use Bugherd\Exception\InvalidArgumentException;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldPassApiKeyToContructor()
    {
        $client = new Client('asdf');

        $this->assertInstanceOf('Bugherd\Client', $client);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldNotGetApiInstance()
    {
        $client = new Client('asdf');
        $client->api('do_not_exist');
    }

    /**
     * @test
     * @dataProvider getApiClassesProvider
     */
    public function shouldGetApiInstance($apiName, $class)
    {
        $client = new Client('asdf');
        $this->assertInstanceOf($class, $client->api($apiName));
    }


    public function getApiClassesProvider()
    {
        return array(
            array('organization', 'Bugherd\Api\Organization'),
            array('user', 'Bugherd\Api\User'),
            array('project', 'Bugherd\Api\Project'),
            array('task', 'Bugherd\Api\Task'),
            array('comment', 'Bugherd\Api\Comment'),
            array('webhook', 'Bugherd\Api\Webhook')
        );
    }
}
