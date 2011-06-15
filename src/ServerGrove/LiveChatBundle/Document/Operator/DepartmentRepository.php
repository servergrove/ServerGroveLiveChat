<?php

namespace ServerGrove\LiveChatBundle\Document\Operator;

use ServerGrove\LiveChatBundle\Document\DocumentRepository;

/**
 * Description of DepartmentRepository
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class DepartmentRepository extends DocumentRepository
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