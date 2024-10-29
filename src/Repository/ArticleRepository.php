<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function add(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findPublishedArticle(int $page = 0): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isPublished = :val')
            ->setParameter('val', true)
            ->orderBy('a.id', 'DESC')
            ->setFirstResult($page)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findUnpublishedArticle(): array
    {
        $queryResult = $this->createQueryBuilder('a')
            ->andWhere('a.isPublished = :val')
            ->setParameter('val', false)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();

        return $queryResult;
    }

   public function findOneById(int $value): ?Article
   {
       return $this->createQueryBuilder('a')
           ->andWhere('a.id = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

   public function findCount(): ?int
   {
         $count = $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getOneOrNullResult();

        return $count[1];
   }
}
