<?php

namespace ServerGrove\SGLiveChatBundle\Tests\Document;

use Servergrove\SGLiveChatBundle\Tests\TestCase;

abstract class RepositoryTestCase extends TestCase {
	
	private $repository;
	

	/**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->repository = $this->getContainer()->get('doctrine.odm.mongodb.document_manager')->getRepository('SGLiveChatBundle:' . $this->getDocumentName());
    }

    protected function getDocumentName() {
        $className = str_replace(__NAMESPACE__ . '\\', '', get_class($this));
        
        $o = null;
        if (preg_match('/^([A-Z][a-zA-Z0-9]+)RepositoryTest$/', $className, $o)) {
            return $o[1];
        }
        
        throw new \Exception('Invalid class name for test');
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->repository = null;
        parent::tearDown();
    }

    
    /**
     * @return Doctrine\ODM\MongoDB\DocumentRepository
     */
	protected function getRepository() {
		return $this->repository;
	}
}