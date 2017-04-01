<?php

namespace Tests\AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testResetPassword()
    {
        exec('./bin/console d:d:c --env=test');
        exec('./bin/console d:s:c --env=test');
        exec('./bin/console h:f:l -n --env=test');

        $client = static::createClient();
        $content = '{"email":"1"}';
        $client->request(
            'POST',
            '/api/password_reset',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            $content

        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $json = '{            
                "email": "intern1@seattle.com"          
        }';

        $client->request('POST', '/api/password_reset', [], [], ['CONTENT_TYPE' => 'application/json'], $json
        );
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            ),
            'the "Content-Type" header is "application/json"'
        );
        $this->assertTrue($client->getResponse()->isSuccessful(), 'response status is 2xx');
        exec('./bin/console d:d:d --force --env=test');
    }
}
