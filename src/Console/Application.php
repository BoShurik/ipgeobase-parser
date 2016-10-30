<?php
/**
 * User: boshurik
 * Date: 28.10.16
 * Time: 15:37
 */

namespace BoShurik\IPGeoBase\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

class Application extends BaseApplication
{
    /**
     * @var Command
     */
    private $defaultCommand;

    /**
     * @inheritDoc
     */
    public function __construct($name, $version, Command $defaultCommand)
    {
        $this->defaultCommand = $defaultCommand;

        parent::__construct($name, $version);
    }

    /**
     * @inheritDoc
     */
    protected function getCommandName(InputInterface $input)
    {
        return $this->defaultCommand->getName();
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultCommands()
    {
        // Keep the core default commands to have the HelpCommand
        // which is used when using the --help option
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = $this->defaultCommand;

        return $defaultCommands;
    }

    /**
     * @inheritDoc
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        // clear out the normal first argument, which is the command name
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}