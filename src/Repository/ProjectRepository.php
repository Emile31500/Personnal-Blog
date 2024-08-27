<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function add(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByUnpublishedProject(int $page = 0): array
    {

        // ->from(Project::class, 'p')
        $result = $this->createQueryBuilder('p') 
            ->andWhere('p.isPublished = :val')
            ->setParameter('val', false)
            ->setMaxResults(10)
            ->setFirstResult($page)
            ->getQuery()
            ->getResult();

        return $result;
        //  ->join('p.media','pm')
    }

    public function findByPublishedProject(int $page = 0): array
    {

        // ->from(Project::class, 'p')
        $result = $this->createQueryBuilder('p')
            ->join('p.media','pm')
            ->andWhere('p.isPublished = :val')
            ->setParameter('val', true)
            ->setMaxResults(10)
            ->setFirstResult($page)
            ->getQuery()
            ->getResult();

        return $result;
        //  ->join('p.media','pm')
    }

    public function findOneById(int $id): ?Project
    {
        $result = $this->createQueryBuilder('p')
            ->join('p.media','pm')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
            // ->from(Project::class, 'p')

        return $result;
    }
}
