<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Entity\Operation;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    //    /**
    //     * @return Notification[] Returns an array of Notification objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Notification
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findUnreadByUser(User $user, Operation $operation)
    {
        return $this->createQueryBuilder('n')
            ->select('n', 'CASE WHEN nr.id IS NOT NULL THEN true ELSE false END as isRead')
            ->leftJoin('n.notificationReads', 'nr', 'WITH', 'nr.user = :user')
            ->where('n.operation = :operation')
            ->setParameter('user', $user)
            ->setParameter('operation', $operation)
            ->orderBy('CASE WHEN nr.id IS NULL THEN 0 ELSE 1 END', 'ASC')
            ->addOrderBy('n.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function createNotification(bool $flush = true, Operation $operation, string $content, User $source = null, string $targetPath = null)
    {
        $notification = new Notification();
        $notification->setOperation($operation);
        $notification->setContent($content);
        $notification->setSource($source);
        $notification->setTargetPath($targetPath);
        $this->getEntityManager()->persist($notification);

        if($flush) {
            $this->getEntityManager()->flush();
        }

        return $notification;
    }
}
