<?php

namespace ServerGrove\SGLiveChatBundle\Document\Operator;

use ServerGrove\SGLiveChatBundle\Document\DocumentRepository;

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