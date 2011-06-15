<?php

namespace ServerGrove\LiveChatBundle\Document;

use ServerGrove\LiveChatBundle\Document\DocumentRepository;
use ServerGrove\LiveChatBundle\Document\Operator;

/**
 * Description of RatingRepository
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