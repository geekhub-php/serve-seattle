<?php

namespace tests\AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiDefaultControllerTest extends WebTestCase
{
    public function testLogin()
    {
        exec('./bin/console d:d:c --env=test');
        exec('./bin/console d:s:c --env=test');
        exec('./bin/console h:f:l -n --env=test');

        $client = static::createClient();

        $json = '{
            "user": {
                "email": "intern1@seattle.com",
                "password":"intern1"
            }
        }';

        $client->request('POST', '/api/login', [], [], [], $json
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $content = $client->getResponse()->getContent();

        $arrayContent = json_decode($content, true);

        $token = $arrayContent['X-AUTH-TOKEN'];

        $headers = ['HTTP_X-AUTH-TOKEN' => $token];

        $client->request('GET', '/api/user', [], [], $headers);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $headers = ['HTTP_X-AUTH-TOKEN' => null];

        $client->request('GET', '/api/user', [], [], $headers);

        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        exec('./bin/console d:d:d --force --env=test');
    }
}
