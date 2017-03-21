<?php

namespace Tests\AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SurveyControllerTest extends WebTestCase
{
    public function testList()
    {
        exec('./bin/console d:d:c --env=test');
        exec('./bin/console d:s:c --env=test');
        exec('./bin/console h:f:l -n --env=test');

        $client = static::createClient();

        $client->request('GET', '/api/surveys');

        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            '/api/surveys',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => '1')

        );
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            ),
            'the "Content-Type" header is "application/json"'
        );
        $this->assertTrue($client->getResponse()->isSuccessful(), 'response status is 2xx');
    }

    public function testShow()
    {
        $client = static::createClient();

        $client->request('GET', '/api/surveys/1');

        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            '/api/surveys',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => '1')

        );
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            ),
            'the "Content-Type" header is "application/json"'
        );
        $this->assertTrue($client->getResponse()->isSuccessful(), 'response status is 2xx');
    }

    public function testEdit()
    {
        $client = static::createClient();

        $client->request('PUT', '/api/surveys/1');

        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        $client->request(
            'PUT',
            '/api/surveys/2',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json',
                  'HTTP_X-AUTH-TOKEN' => '2', ),
            $content = '{"1":"yes","2":"yes","3":"yes","4":"5","5":"7"}'
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        exec('./bin/console d:d:d --force --env=test');
    }
}
