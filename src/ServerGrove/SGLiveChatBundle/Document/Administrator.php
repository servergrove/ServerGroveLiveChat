<?php

namespace ServerGrove\SGLiveChatBundle\Document;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 *
 * @mongodb:Document
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
