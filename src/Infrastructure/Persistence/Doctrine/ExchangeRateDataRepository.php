<?php
/**
 * ExchangeRateDataRepository.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ExchangeRate\Domain\Entity\ExchangeRateData;
use Exception;
use Error;

class ExchangeRateDataRepository extends AbstractDoctrineRepository
{
    /**
     * @var EntityRepository
     */
    private EntityRepository $repository;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->repository = $em->getRepository(ExchangeRateData::class);
    }

    /**
     * @param string $rateDate
     * @param string $quoteCurrency
     * @param string $baseCurrency
     * @param string $source
     * @return float|false
     */
    public function getRow(string $rateDate, string $quoteCurrency, string $baseCurrency, string $source): float|false
    {
        try {
            $result = $this->repository
                ->createQueryBuilder('erd')
                ->select('erd.rateValue')
                ->where('erd.rateDate = :rateDate')
                ->andWhere('erd.quoteCurrency = :quoteCurrency')
                ->andWhere('erd.baseCurrency = :baseCurrency')
                ->andWhere('erd.source = :source')
                ->setParameter('rateDate', new \DateTimeImmutable($rateDate))
                ->setParameter('quoteCurrency', strtoupper($quoteCurrency))
                ->setParameter('baseCurrency', strtoupper($baseCurrency))
                ->setParameter('source', strtolower($source))
                ->getQuery()
                ->getOneOrNullResult();
            if (!$result) {
                return false;
            }
            return (double) $result['rateValue'];
        } catch (Exception $e) {
            return false;
        } catch (Error $e) {
            return false;
        }
    }

    /**
     * @param string $rateDate
     * @param string $quoteCurrency
     * @param string $baseCurrency
     * @param string $source
     * @return bool
     */
    public function checkRow(string $rateDate, string $quoteCurrency, string $baseCurrency, string $source): bool
    {
        try {
            $result = $this->repository
                ->createQueryBuilder('erd')
                ->select('erd.rateId')
                ->where('erd.rateDate = :rateDate')
                ->andWhere('erd.quoteCurrency = :quoteCurrency')
                ->andWhere('erd.baseCurrency = :baseCurrency')
                ->andWhere('erd.source = :source')
                ->setParameter('rateDate', new \DateTimeImmutable($rateDate))
                ->setParameter('quoteCurrency', $quoteCurrency)
                ->setParameter('baseCurrency', $baseCurrency)
                ->setParameter('source', $source)
                ->getQuery()
                ->getOneOrNullResult();
            return (bool) $result;
        } catch (Exception $e) {
            return false;
        } catch (Error $e) {
            return false;
        }
    }

    /**
     * @param string $rateDate
     * @param string $quoteCurrency
     * @param string $baseCurrency
     * @param string $source
     * @param float $rateValue
     * @return bool
     */
    public function createRow(string $rateDate, string $quoteCurrency, string $baseCurrency, string $source, float $rateValue): bool
    {
        try {
            $exchangeRateData = new ExchangeRateData(
                new \DateTimeImmutable($rateDate),
                $baseCurrency,
                $quoteCurrency,
                (string) $rateValue,
                $source
            );
            $this->em->persist($exchangeRateData);
            $this->em->flush();
            return true;
        } catch (Exception $e) {
            return false;
        } catch (Error $e) {
            return false;
        }
    }
}
