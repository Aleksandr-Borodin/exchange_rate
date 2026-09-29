<?php
/**
 * GetRateCommand.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Presentation\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ExchangeRate\Application\Service\GetRateService;

#[AsCommand(
    name: 'rate:get',
    description: 'Get exchange rate for a specific date and currency',
)]
class GetRateCommand extends Command
{
    /**
     * @param GetRateService $_grService
     */
    public function __construct(private readonly GetRateService $_grService)
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument(
                'rateDate',
                InputArgument::REQUIRED,
                'Rate date (Y-m-d)'
            )
            ->addArgument(
                'quoteCurrency',
                InputArgument::REQUIRED,
                'Quote currency'
            )
            ->addArgument(
                'baseCurrency',
                InputArgument::OPTIONAL,
                'Base currency',
                'RUB'
            )
            ->addOption(
                'source',
                null,
                InputOption::VALUE_REQUIRED,
                'Rate source',
                'cbr'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rateDate = (string) $input->getArgument('rateDate');
        $quoteCurrency = strtoupper((string) $input->getArgument('quoteCurrency'));
        $baseCurrency = strtoupper((string) $input->getArgument('baseCurrency'));
        $source = strtolower((string) $input->getOption('source'));
        if(!$rateDate || !$quoteCurrency || !$baseCurrency || !$source) {
            return Command::FAILURE;
        }
        $result = $this->_grService->getRate($rateDate, $quoteCurrency, $baseCurrency, $source);
        $this->_printResult($output, $result);
        return Command::SUCCESS;
    }

    /**
     * @param OutputInterface $output
     * @param array $result
     * @return void
     */
    private function _printResult(OutputInterface $output, array $result): void
    {
        $fields = [
            'rate_date' => 'Rate date',
            'quote_currency' => 'Quote currency',
            'base_currency' => 'Base currency',
            'rate_value' => 'Rate value',
            'difference' => 'Difference',
            'status' => 'Status',
            'source' => 'Source',
        ];
        $message = '';
        foreach ($fields as $key => $label) {
            if (empty($result[$key])) { continue; }
            $message .= ($message ? "\n" : '') . $label . ': ' . $result[$key];
        }
        if ($message !== '') {
            $output->writeln($message);
        }
    }
}
