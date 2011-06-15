<?php

namespace ServerGrove\SGLiveChatBundle\Command;

use ServerGrove\SGLiveChatBundle\Document\Administrator;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class AddAdministratorCommand extends AddOperatorCommand
{

    /**
     * @see Command
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('sglivechat:admin:add-administrator');
        $this->setDescription('Create new Administrator');
    }

    public function createOperator()
    {
        return new Administrator();
    }
}