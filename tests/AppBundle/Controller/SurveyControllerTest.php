<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SurveyControllerTest extends WebTestCase
{
    public function testSurveys()
    {
        exec('./bin/console d:d:c --env=test');
        exec('./bin/console d:s:c --env=test');
        exec('./bin/console h:f:l -n --env=test');

        $client = static::createClient();

        $crawler = $client->request('GET', '/surveys', array(), array(), array(
            'PHP_AUTH_USER' => 'admin@serve-seattle.com',
            'PHP_AUTH_PW' => 'admin',
        ));

        $form = $crawler->selectButton('Filter')->form();
        $form['survey_filter[type]']->select('1');

        $client->followRedirects();
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('ul.survey_item'));
    }

    public function testSurvey()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/surveys/1', array(), array(), array(
            'PHP_AUTH_USER' => 'admin@serve-seattle.com',
            'PHP_AUTH_PW' => 'admin',
        ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(1, $crawler
            ->filter('h3:contains("Internship survey")')
            ->count());
    }

    public function testSurveyCreate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/surveys/create/internship', array(), array(), array(
            'PHP_AUTH_USER' => 'admin@serve-seattle.com',
            'PHP_AUTH_PW' => 'admin',
        ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Create survey')->form();

        $form['survey[user]']->select('2');

        $client->followRedirects();
        $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testSurveyDelete()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/surveys/delete/1', array(), array(), array(
            'PHP_AUTH_USER' => 'admin@serve-seattle.com',
            'PHP_AUTH_PW' => 'admin',
        ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Delete survey')->form();

        $client->followRedirects();
        $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        exec('./bin/console d:d:d --force --env=test');
    }
}
