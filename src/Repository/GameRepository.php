<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function save(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //запрос на получение всех игр
    public function findAllGame(): array
    {
        return $this->createQueryBuilder('game')
            ->select('game.id','game.name','developer.name developer_name')
            ->addSelect("GROUP_CONCAT(genre.name SEPARATOR ', ') as genre_name")
            ->leftJoin('game.developer','developer')
            ->leftJoin('game.genre','genre')
            ->groupBy('game.id')
            ->orderBy('game.id','ASC')
            ->getQuery()
            ->getResult();
    }

    //запрос на поиск игры по жанру
    public function findGameByGenre($genreId): array
    {
        return $this->createQueryBuilder('game')
            ->select('game.id','game.name','developer.name developer_name')
            ->addSelect("GROUP_CONCAT(genre.name SEPARATOR ', ') as genre_name")
            ->andWhere('genre.id LIKE :val')
            ->leftJoin('game.developer','developer')
            ->leftJoin('game.genre','genre')
            ->setParameter('val', $genreId)
            ->groupBy('game.id')
            ->getQuery()
            ->getResult();
    }
}
