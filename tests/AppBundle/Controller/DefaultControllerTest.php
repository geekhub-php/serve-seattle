<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        exec('./bin/console d:d:c --env=test');
        exec('./bin/console d:s:c --env=test');
        exec('./bin/console h:f:l -n --env=test');

        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertCount(3, $crawler->filter('input'));
    }

    public function testUpdatePassword()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/password_update/2');
        $this->assertEquals('200', $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Submit')->form();
        $form['reset_password[plainPassword][first]'] = 'new';
        $form['reset_password[plainPassword][second]'] = 'new';
        $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        exec('./bin/console d:d:d --force --env=test');
    }
}
