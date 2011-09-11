<?php

namespace ServerGrove\LiveChatBundle\Document\Operator;

use ServerGrove\LiveChatBundle\Document\DocumentRepository;
use ServerGrove\LiveChatBundle\Document\Operator;

/**
 * Rating repository class
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class RatingRepository extends DocumentRepository
{

    public function getOperatorRatings(Operator $operator)
    {
        return $this->findBy(array('chatOperatorId' => $operator));
    }

}