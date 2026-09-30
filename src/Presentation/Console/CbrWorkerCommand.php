<?php
/**
 * CbrWorkerCommand.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Presentation\Console;

use ExchangeRate\Application\Service\CbrWorkerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'rate:worker',
    description: 'Get exchange rates from CBR'
)]
class CbrWorkerCommand extends Command
{
    /**
     * @param CbrWorkerService $_cwService
     */
    public function __construct(private readonly CbrWorkerService $_cwService)
    {
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Worker started.');
        $this->_cwService->run();
        return Command::SUCCESS;
    }
}
