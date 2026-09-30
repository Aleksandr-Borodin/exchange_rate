<?php
/**
 * ExchangeRateDayRepository.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Infrastructure\Persistence\Doctrine;

use ExchangeRate\Domain\Enum\ExchangeRateDayStatus;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ExchangeRate\Domain\Entity\ExchangeRateDay;
use DateTimeImmutable;
use Exception;
use Error;

class ExchangeRateDayRepository extends AbstractDoctrineRepository
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
        $this->repository = $em->getRepository(ExchangeRateDay::class);
    }

    /**
     * @param array $daysList
     * @param string $source
     * @return array
     */
    public function findExistingDates(array $daysList, string $source): array
    {
        try {
            $dates = array_keys($daysList);
            $qb = $this->repository->createQueryBuilder('erd');
            $result = $qb
                ->select('erd.rateDate')
                ->where($qb->expr()->in('erd.rateDate', ':dates'))
                ->andWhere('erd.source = :source')
                ->setParameter('dates', $dates)
                ->setParameter('source', $source)
                ->getQuery()
                ->getArrayResult();
            return array_map(
                static fn (DateTimeImmutable $date): string => $date->format('Y-m-d'),
                array_column($result, 'rateDate')
            );
        } catch(Exception $e) {
            return [];
        }
    }

    /**
     * @param string $rateDate
     * @param string $source
     * @return bool
     */
    public function writeRateDate(string $rateDate, string $source): bool
    {
        try {
            $exchangeRateDay = new ExchangeRateDay(new DateTimeImmutable($rateDate), $source);
            $this->em->persist($exchangeRateDay);
            $this->em->flush();
            return true;
        } catch (Exception $e) {
            return false;
        } catch (Error $e) {
            return false;
        }
    }

    /**
     * @param string $rateDate
     * @param string $source
     * @return string|bool
     */
    public function getRowStatus(string $rateDate, string $source): string|bool
    {
        try {
            $result = $this->repository
                ->createQueryBuilder('erd')
                ->select('erd.status')
                ->where('erd.rateDate = :rateDate')
                ->andWhere('erd.source = :source')
                ->setParameter('rateDate', new DateTimeImmutable($rateDate))
                ->setParameter('source', $source)
                ->getQuery()
                ->getOneOrNullResult();
            if (!$result) {
                return false;
            }
            return $result['status']->value;
        } catch (Exception $e) {
            return false;
        } catch (Error $e) {
            return false;
        }
    }

    /**
     * @param string $rateDate
     * @param string $source
     * @param string $status
     * @return bool
     */
    public function updateStatus(string $rateDate, string $source, string $status): bool
    {
        try {
            $exchangeRateDay = $this->repository
                ->createQueryBuilder('erd')
                ->where('erd.rateDate = :rateDate')
                ->andWhere('erd.source = :source')
                ->setParameter('rateDate', new DateTimeImmutable($rateDate))
                ->setParameter('source', $source)
                ->getQuery()
                ->getOneOrNullResult();
            if (!$exchangeRateDay) {
                return false;
            }
            if ($status === ExchangeRateDayStatus::PROCESSING->value) {
                $exchangeRateDay->markProcessing();
                $exchangeRateDay->incrementAttempts();
            } elseif ($status === ExchangeRateDayStatus::COMPLETED->value) {
                $exchangeRateDay->markCompleted();
            } elseif ($status === ExchangeRateDayStatus::FAILED->value) {
                $exchangeRateDay->markFailed();
            }
            $this->em->flush();
            return true;
        } catch (Exception $e) {
            return false;
        } catch (Error $e) {

            return false;
        }
    }
}
