<?php

namespace Tests\AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Admin;
use AppBundle\Entity\User;

class AdminControllerTest extends AbstractController
{
    public function testIndexAction()
    {
        $this->request('/admin/', 'GET', 302);
        $this->request('/login');
        $client = $this->logIn();
        $crawler = $this->request('/admin/');

        $form = $crawler->selectButton('Save')->form();
        $client->submit($form, [
            'admin[userName]' => 'test1',
            'admin[email]' => 'test1@gmail.com',
        ]);
        $admin = $this->em->getRepository(Admin::class)->findOneBy(['userName' => 'test1']);

        $this->assertInstanceOf(Admin::class, $admin);
    }
}
