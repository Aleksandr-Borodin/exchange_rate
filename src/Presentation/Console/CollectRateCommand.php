<?php
/**
 * CollectRateCommand.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Presentation\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ExchangeRate\Application\Service\CollectRateService;

#[AsCommand(
    name: 'cbr:collect',
    description: 'run tasks for worker',
)]
class CollectRateCommand extends Command
{
    /**
     * @param CollectRateService $_crService
     */
    public function __construct(private readonly CollectRateService $_crService)
    {
        parent::__construct();
    }
    
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument('days', InputArgument::REQUIRED)
            ->addArgument('source', InputArgument::OPTIONAL, 'Rate source', 'cbr');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $days = (int) $input->getArgument('days');
        $source = (string) $input->getArgument('source');
        if($days && $source) {
            $this->_crService->collectDays($days, $source);
            return Command::SUCCESS;
        }
        return Command::FAILURE;
    }
}
