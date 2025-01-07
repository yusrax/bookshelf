<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * Get reviews with optional sorting
     *
     * @param array $criteria Sorting criteria, e.g., ['createdAt' => 'DESC']
     * @return Review[]
     */
    public function findAllSorted(array $criteria = ['createdAt' => 'DESC']): array
    {
        $qb = $this->createQueryBuilder('r')
            ->orderBy(key($criteria), current($criteria));

        return $qb->getQuery()->getResult();
    }

    public function createQueryForSorting(array $orderBy): Query
    {
        $qb = $this->createQueryBuilder('r');

        // Apply sorting
        foreach ($orderBy as $field => $direction) {
            $qb->addOrderBy($field, $direction);
        }

        return $qb->getQuery();
    }

    public function createSearchQuery(string $searchQuery, array $orderBy): Query
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.book', 'b') // Join with the Book entity
            ->addSelect('b'); // Select book fields

        if (!empty($searchQuery)) {
            $qb->andWhere('LOWER(r.reviewText) LIKE :query OR LOWER(b.title) LIKE :query OR LOWER(b.author) LIKE :query')
                ->setParameter('query', '%' . strtolower($searchQuery) . '%');
        }

        // Apply sorting
        foreach ($orderBy as $field => $direction) {
            $qb->addOrderBy($field, $direction);
        }

        return $qb->getQuery();
    }

    public function createQueryForBook(Book $book, array $orderBy, string $searchQuery = ''): Query
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.book = :book')
            ->setParameter('book', $book);

        // Apply search filter
        if (!empty($searchQuery)) {
            $qb->andWhere('r.reviewText LIKE :searchQuery OR r.user.username LIKE :searchQuery')
                ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        // Apply sorting
        foreach ($orderBy as $field => $direction) {
            $qb->addOrderBy($field, $direction);
        }

        return $qb->getQuery();
    }




//    /**
//     * @return Review[] Returns an array of Review objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Review
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
