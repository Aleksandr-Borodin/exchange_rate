<?php
/**
 * AbstractDoctrineRepository.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

namespace ExchangeRate\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractDoctrineRepository
{
    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param object $entity
     * @return void
     */
    protected function persist(object $entity): void
    {
        $this->em->persist($entity);
    }

    /**
     * @param object $entity
     * @return void
     */
    protected function remove(object $entity): void
    {
        $this->em->remove($entity);
    }

    /**
     * @return void
     */
    protected function flush(): void
    {
        $this->em->flush();
    }

    /**
     * @param object $entity
     * @param bool $flush
     * @return void
     */
    public function save(object $entity, bool $flush = true): void
    {
        $this->em->persist($entity);
        if ($flush) {
            $this->em->flush();
        }
    }

    /**
     * @param object $entity
     * @param bool $flush
     * @return void
     */
    public function delete(object $entity, bool $flush = true): void
    {
        $this->em->remove($entity);
        if ($flush) {
            $this->em->flush();
        }
    }

    /**
     * @return EntityManagerInterface
     */
    protected function em(): EntityManagerInterface
    {
        return $this->em;
    }
}
