<?php
/**
 * User: boshurik
 * Date: 28.10.16
 * Time: 15:36
 */

namespace BoShurik\IPGeoBase\Console;

use BoShurik\IPGeoBase\Parser\Parser;
use BoShurik\IPGeoBase\Persister\PersisterFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ParseCommand extends Command
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var PersisterFactory
     */
    private $persisterFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var StyleInterface
     */
    private $io;

    public function __construct($name, Parser $parser, PersisterFactory $persisterFactory, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($name);

        $this->parser = $parser;
        $this->persisterFactory = $persisterFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function configure()
    {
        $this
            ->addArgument('path', InputArgument::REQUIRED, 'Path to zip archive')
            ->addArgument('database', InputArgument::REQUIRED, 'Database to import to')
            ->addOption('host', null, InputOption::VALUE_REQUIRED, 'Database host', 'localhost')
            ->addOption('user', null, InputOption::VALUE_REQUIRED, 'Database user', 'root')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Database password', null)
            ->addOption('city-table', null, InputOption::VALUE_REQUIRED, 'City table name', 'city')
            ->addOption('range-table', null, InputOption::VALUE_REQUIRED, 'Range table name', 'range')
            ->setDescription('Parse ipgeobase.ru zip archive')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $outputAdapter = new OutputAdapter($this->io);
        $persister = $this->persisterFactory->createPersister(
            $input->getOption('host'),
            $input->getOption('user'),
            $input->getOption('password'),
            $input->getArgument('database'),
            $input->getOption('city-table'),
            $input->getOption('range-table')
        );

        $this->eventDispatcher->addSubscriber($outputAdapter);
        $this->eventDispatcher->addSubscriber($persister);

        $this->parser->parse($input->getArgument('path'));
    }
}