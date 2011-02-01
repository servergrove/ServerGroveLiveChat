<?php

namespace Application\ChatBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Application\ChatBundle\Document\Operator;

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