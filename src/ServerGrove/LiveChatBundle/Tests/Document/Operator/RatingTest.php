<?php

namespace ServerGrove\LiveChatBundle\Tests\Document\Operator;

use ServerGrove\LiveChatBundle\Tests\TestCase;
use ServerGrove\LiveChatBundle\Document\Operator\Rating;

class RatingTest extends TestCase
{
    public function testOverrideSession()
    {
        $this->setExpectedException('\BadMethodCallException');
        $rating = new Rating($this->getTestSession());
        $rating->setSession($this->getTestSession());
    }

    public function testSetOperator()
    {
        $session = $this->getTestSession();
        $session->setOperator($this->getTestOperator());
        $rating = new Rating($session);
        $this->saveDocument($rating);
        $this->removeDocumentOnShutdown($rating);
        $this->assertEquals($session->getOperator()->getId(), $rating->getOperator()->getId());

        $session = $this->getTestSession();
        $rating = new Rating($session);
        $session->setOperator($this->getTestOperator());
        $rating->setOperator($this->getTestOperator());
        $this->saveDocument($rating);
        $this->removeDocumentOnShutdown($rating);
        $this->assertNotEquals($session->getOperator()->getId(), $rating->getOperator()->getId());
    }
}
