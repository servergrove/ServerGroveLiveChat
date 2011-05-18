<?php

namespace ServerGrove\SGLiveChatBundle\Document;

use ServerGrove\SGLiveChatBundle\Document\DocumentRepository;
use ServerGrove\SGLiveChatBundle\Document\Operator;

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