<?php

namespace App\Repository;

use App\Entity\CargoItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CargoItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CargoItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CargoItem[]    findAll()
 * @method CargoItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CargoItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CargoItem::class);
    }

    /**
     * Поиск товаров
     * @param null $repeat
     * @param null $title
     * @return array
     */
    public function findItem($repeat = null, $title = null): array
    {
        $qb = $this->createQueryBuilder('i')
            ->select('i.title')
            ->groupBy('i.title');

        if (!empty($repeat)) {
            $qb
                ->having('COUNT(i.title) = :repeat')
                ->setParameter('repeat', $repeat);
        }

        if (!empty($title)) {
            $qb
                ->where('i.title IN (:title)')
                ->setParameter('title', $title);
        }

        $result = $qb->getQuery()->getResult();
        return array_map(static function ($a) {
            return $a['title'];
        }, $result);
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

        $return = $qb->getQuery()->getResult();
        return $return[0]['id'] ?? null;
    }
}
