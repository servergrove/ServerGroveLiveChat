<?php

namespace Application\ChatBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChatControllerTest extends WebTestCase
{

    public function testIndex()
    {
        $client = $this->createClient();
        
        $crawler = $client->request('GET', '/');
        
        $this->assertTrue($crawler->filter('html:contains("Start Chat")')
            ->count() > 0);
    }
}