<?php

namespace Tests\AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SurveyControllerTest extends WebTestCase
{
    public function testApiSurveys()
    {
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

    public function testApiSurvey()
    {
        $client = static::createClient();

        $client->request('GET', '/api/survey/1');

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

    public function testApiSurveyUpdate()
    {
        $client = static::createClient();

        $client->request('POST', '/api/survey/update/1');

        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            '/api/survey/update/2',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json',
                  'HTTP_X-AUTH-TOKEN' => '2', ),
            $content = '{"1":"yes","2":"yes","3":"yes","4":"5","5":"7"}'
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
