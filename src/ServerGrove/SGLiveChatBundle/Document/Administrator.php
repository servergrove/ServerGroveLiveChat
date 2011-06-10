<?php

namespace ServerGrove\SGLiveChatBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 *
 * @MongoDB\Document
 */
class Administrator extends Operator
{

    /**
     * @return array
     */
    public function getRoles()
    {
        return array('ROLE_USER', 'ROLE_ADMIN');
    }
}
