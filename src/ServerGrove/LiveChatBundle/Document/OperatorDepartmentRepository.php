<?php

namespace ServerGrove\LiveChatBundle\Document;

use ServerGrove\LiveChatBundle\Document\DocumentRepository;

/**
 * Description of OperatorDepartmentRepository
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class OperatorDepartmentRepository extends DocumentRepository
{

    public function getDepartments()
    {
        return $this->createQueryBuilder()->field('isActive')
                ->equals(true)
                ->sort('name')
                ->getQuery()
                ->execute();
    }

}