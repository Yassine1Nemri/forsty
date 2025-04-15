<?php
// src/Repository/MessengerMessageRepository.php
namespace App\Repository;

use App\Entity\MessengerMessage;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessengerMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessengerMessage::class);
    }

    public function findConversation(User $user1, User $user2)
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :user1 AND m.receiver = :user2) OR (m.sender = :user2 AND m.receiver = :user1)')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->orderBy('m.sentAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findUnreadMessagesCount(User $user): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.receiver = :user')
            ->andWhere('m.isRead = :isRead')
            ->setParameter('user', $user)
            ->setParameter('isRead', false)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findUserConversations(User $user)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $sentMessages = $qb->select('IDENTITY(m.receiver) as userId')
            ->from(MessengerMessage::class, 'm')
            ->where('m.sender = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $receivedMessages = $qb->select('IDENTITY(m.sender) as userId')
            ->from(MessengerMessage::class, 'm')
            ->where('m.receiver = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $userIds = array_unique(array_merge(
            array_column($sentMessages, 'userId'),
            array_column($receivedMessages, 'userId')
        ));

        return $this->getEntityManager()->getRepository(User::class)->findBy(['id' => $userIds]);
    }
}
