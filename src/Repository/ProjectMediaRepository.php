<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\ProjectMedia;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ProjectMediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectMedia::class);
    }

    public function add(ProjectMedia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProjectMedia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return ProjectMedia[] Returns an array of ProjectMedia objects
     */
    public function findAllByProject(Project $project): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.project = :id')
            ->setParameter('id', $project->getId())
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    // public function findOneBySomeField($value): ?ProjectMedia
    // {
    //     return $this->createQueryBuilder('p')
    //         ->andWhere('p.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }
}
