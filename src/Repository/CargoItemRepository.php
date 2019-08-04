<?php

namespace App\Repository;

use App\Entity\CargoItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CargoItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CargoItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CargoItem[]    findAll()
 * @method CargoItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CargoItemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CargoItem::class);
    }

    /**
     * Поиск товаров
     * @param null $repeat
     * @param null $title
     * @return mixed
     */
    public function findItem($repeat = null, $title = null): array
    {
        // select DISTINCT(title), count(title) from cargo_item group by title  order by count(title);
        $qb = $this->createQueryBuilder('i')
            ->select('i.title')
            ->groupBy('i.title');

        if (null !== $repeat) {
            $qb
                ->having('COUNT(i.title) = :repeat')
                ->setParameter('repeat', $repeat);
        }

        if (null !== $title) {
            $qb
                ->where('i.title IN (:title)')
                ->setParameter('title', $title);
        }

        $result = $qb->getQuery()->getResult();
        $result = array_map(static function ($a) {
            return $a['title'];
        }, $result);

        return $result;
    }

    /**
     * Псевдо функция проверки наличия фото
     * @param $item
     * @return array
     */
    public function noPictures($item): array
    {
        $noPictureArray = [];
        foreach ($item as $value) {
            if ($value < 101) {
                $noPictureArray[] = $value;
            }
        }

        return $noPictureArray;
    }

    /**
     * @param array|null $items
     * @return string|null
     * @throws NonUniqueResultException
     */
    public function getCargoByMaxWeightOfUniqueItems(array $items = null): ?string
    {
        $qb = $this->createQueryBuilder('i')
            ->join('i.cargo', 'c')
            ->select('c.id')
            ->setMaxResults(1)
            ->groupBy('i.cargo')
            ->orderBy('count(DISTINCT i.title) ', 'DESC');
        if (!empty($items)) {
            $qb->where('i.title IN (:items)')
                ->setParameter('items', $items);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}
