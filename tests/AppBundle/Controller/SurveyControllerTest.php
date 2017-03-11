<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SurveyControllerTest extends WebTestCase
{
    public function testSurveys()
    {
        $client = static::createClient();

        $client->request('GET', '/surveys', array(), array(), array(
            'PHP_AUTH_USER' => 'kaden.collier@waters.com',
            'PHP_AUTH_PW' => 'admin1',
        ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testSurvey()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/survey/1', array(), array(), array(
            'PHP_AUTH_USER' => 'kaden.collier@waters.com',
            'PHP_AUTH_PW' => 'admin1',
        ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(1, $crawler
            ->filter('h3:contains("Internship survey")')
            ->count());
    }

    public function testSurveyCreate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/survey/create/internship', array(), array(), array(
            'PHP_AUTH_USER' => 'kaden.collier@waters.com',
            'PHP_AUTH_PW' => 'admin1',
        ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Create survey')->form();

        $form['survey[user]']->select('3');

        $client->submit($form);
        $client->followRedirects();

        $this->assertTrue(
            $client->getResponse()->isRedirect('/'),
            'response is a redirect to homepage'
        );
    }

    public function testSurveyDelete()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/survey/delete/4', array(), array(), array(
            'PHP_AUTH_USER' => 'kaden.collier@waters.com',
            'PHP_AUTH_PW' => 'admin1',
        ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Delete survey')->form();

        $client->submit($form);
        $client->followRedirects();

        $this->assertTrue(
            $client->getResponse()->isRedirect('/'),
            'response is a redirect to homepage'
        );
    }
}
