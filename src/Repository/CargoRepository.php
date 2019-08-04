<?php

namespace App\Repository;

use App\Entity\Cargo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Cargo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cargo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cargo[]    findAll()
 * @method Cargo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CargoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cargo::class);
    }

    public function cargoByItem(array $title)
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.item', 'i')
            ->where('i.title in (:title)')
            ->setParameter('title', $title);
        return $qb->getQuery()->getResult();
    }
}
