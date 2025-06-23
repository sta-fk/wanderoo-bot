<?php

namespace App\Repository;

use App\Entity\Trip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trip>
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    public function findByShortUuid(string $short): ?Trip
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT * FROM trip WHERE HEX(id) LIKE :short ORDER BY created_at DESC LIMIT 1';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['short' => strtoupper($short) . '%'])->fetchAssociative();

        if (!$result) {
            return null;
        }

        return $this->getEntityManager()->getRepository(Trip::class)->find($result['id']);
    }
}
