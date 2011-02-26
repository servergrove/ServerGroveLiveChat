<?php

namespace ServerGrove\SGLiveChatBundle\Document;


use Symfony\Component\Security\Core\User\AccountInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ODM\MongoDB\DocumentRepository;
use ServerGrove\SGLiveChatBundle\Document\Operator;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use MongoDate;

/**
 * Description of OperatorRepository
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class OperatorRepository extends DocumentRepository implements UserProviderInterface
{

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\Operator
     */
    public function loadUserByAccount(AccountInterface $user)
    {
        if (($user instanceof Operator)) {
            return $user;
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\Operator
     */
    public function loadUserByUsername($username)
    {
        $operator = $this->findOneBy(array(
            'email' => $username));
        if (!$operator) {
            throw new UsernameNotFoundException('Invalid username');
        }

        return $operator;
    }

    public function getOnlineOperatorsCount()
    {
        return $this->createQueryBuilder()->field('isOnline')->equals(true)->getQuery()->count();
    }

    public function closeOldLogins()
    {
        $this->createQueryBuilder()->field('isOnline')->set(false)->field('isOnline')->equals(true)->field('updatedAt')->lt(new MongoDate(time() - 86400))->update()->getQuery()->execute();
    }

    public function supportsClass($class)
    {
        return $class == 'Operator';
    }

}