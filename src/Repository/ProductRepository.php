<?php

namespace App\Repository;

use App\Entity\Product;
use App\Data\SearchData;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * @var $paginator
     */
    protected $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Product::class);
        $this->paginator = $paginator;
    }

    /**
     * Récupère les produits en lien avec une recherche
     *
     * @return PaginationInterface
     */
    public function findSearch(SearchData $search): PaginationInterface
    {
        $query = $this->getSearchQuery($search)->getQuery();
        return $this->paginator->paginate(
            $query,
            $search->page,
            6
        );
    }

    public function countItems(SearchData $search): int
    {
        $query = $this->getSearchQuery($search)->getQuery();
        return count($query->getResult());
    }

/**
 * Récupère le prix minimum et maximum correspondant à une recherche.
 *
 * @param SearchData $search
 * @return int[] Tableau avec les prix minimum et maximum
 */
public function findMinMaxPrice(SearchData $search): array
{
    $queryBuilder = $this->getSearchQuery($search, true, false, false);

    $queryBuilder
        ->select('MIN(p.price) as minPrice', 'MAX(p.price) as maxPrice');

    $results = $queryBuilder->getQuery()->getScalarResult();

    $minPrice = $results[0]['minPrice'] ?? 0; // Valeur par défaut en cas de non-résultat
    $maxPrice = $results[0]['maxPrice'] ?? 0; // Valeur par défaut en cas de non-résultat

    return [(int)$minPrice, (int)$maxPrice];
}

    /**
     * Crée une requête de recherche avec les critères spécifiés.
     *
     * @param SearchData $search
     * @param bool $ignorePrice
     * @param bool $ignoreKms
     * @param bool $ignoreDate
     * @return QueryBuilder
     */
    public function getSearchQuery(SearchData $search, bool $ignorePrice = false, bool $ignoreKms = false, bool $ignoreDate = false): QueryBuilder
    {
        $query = $this->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.category', 'c');

        if (!empty($search->q)) {
            $query->andWhere('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!$ignorePrice && !empty($search->minPrice)) {
            $min = $search->minPrice * 100; // Assurez-vous que cette conversion est nécessaire
            $query->andWhere('p.price >= :min')
                ->setParameter('min', $min);
        }

        if (!$ignorePrice && !empty($search->maxPrice)) {
            $max = $search->maxPrice * 100; // Assurez-vous que cette conversion est nécessaire
            $query->andWhere('p.price <= :max')
                ->setParameter('max', $max);
        }

        if (!empty($search->categories)) {
            $query->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->categories);
        }

        $query->orderBy('p.name', 'ASC');

        return $query;
    }


//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
