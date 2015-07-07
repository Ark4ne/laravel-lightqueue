<?php

use Illuminate\Support\Facades\Artisan;
use Ark4ne\LightQueue\Command\LightQueueCommand;
use Ark4ne\LightQueue\Manager\LightQueueManager;
use Ark4ne\LightQueue\Provider\FileQueueProvider;

class IConsole implements Symfony\Component\Console\Input\InputInterface
{
    /**
     * Returns the first argument from the raw parameters (not parsed).
     *
     * @return string The value of the first argument or null otherwise
     */
    public function getFirstArgument()
    {
        // TODO: Implement getFirstArgument() method.
    }

    /**
     * Returns true if the raw parameters (not parsed) contain a value.
     *
     * This method is to be used to introspect the input parameters
     * before they have been validated. It must be used carefully.
     *
     * @param string|array $values The values to look for in the raw parameters (can be an array)
     *
     * @return bool true if the value is contained in the raw parameters
     */
    public function hasParameterOption($values)
    {
        // TODO: Implement hasParameterOption() method.
    }

    /**
     * Returns the value of a raw option (not parsed).
     *
     * This method is to be used to introspect the input parameters
     * before they have been validated. It must be used carefully.
     *
     * @param string|array $values The value(s) to look for in the raw parameters (can be an array)
     * @param mixed $default The default value to return if no result is found
     *
     * @return mixed The option value
     */
    public function getParameterOption($values, $default = false)
    {
        // TODO: Implement getParameterOption() method.
    }

    /**
     * Binds the current Input instance with the given arguments and options.
     *
     * @param \Symfony\Component\Console\Input\InputDefinition $definition A InputDefinition instance
     */
    public function bind(\Symfony\Component\Console\Input\InputDefinition $definition)
    {
        // TODO: Implement bind() method.
    }

    /**
     * Validates if arguments given are correct.
     *
     * Throws an exception when not enough arguments are given.
     *
     * @throws \RuntimeException
     */
    public function validate()
    {
        // TODO: Implement validate() method.
    }

    /**
     * Returns all the given arguments merged with the default values.
     *
     * @return array
     */
    public function getArguments()
    {
        // TODO: Implement getArguments() method.
    }

    /**
     * Gets argument by name.
     *
     * @param string $name The name of the argument
     *
     * @return mixed
     */
    public function getArgument($name)
    {
        // TODO: Implement getArgument() method.
    }

    /**
     * Sets an argument value by name.
     *
     * @param string $name The argument name
     * @param string $value The argument value
     *
     * @throws \InvalidArgumentException When argument given doesn't exist
     */
    public function setArgument($name, $value)
    {
        // TODO: Implement setArgument() method.
    }

    /**
     * Returns true if an InputArgument object exists by name or position.
     *
     * @param string|int $name The InputArgument name or position
     *
     * @return bool true if the InputArgument object exists, false otherwise
     */
    public function hasArgument($name)
    {
        // TODO: Implement hasArgument() method.
    }

    /**
     * Returns all the given options merged with the default values.
     *
     * @return array
     */
    public function getOptions()
    {
        // TODO: Implement getOptions() method.
    }

    /**
     * Gets an option by name.
     *
     * @param string $name The name of the option
     *
     * @return mixed
     */
    public function getOption($name)
    {
        // TODO: Implement getOption() method.
    }

    /**
     * Sets an option value by name.
     *
     * @param string $name The option name
     * @param string|bool $value The option value
     *
     * @throws \InvalidArgumentException When option given doesn't exist
     */
    public function setOption($name, $value)
    {
        // TODO: Implement setOption() method.
    }

    /**
     * Returns true if an InputOption object exists by name.
     *
     * @param string $name The InputOption name
     *
     * @return bool true if the InputOption object exists, false otherwise
     */
    public function hasOption($name)
    {
        // TODO: Implement hasOption() method.
    }

    /**
     * Is this input means interactive?
     *
     * @return bool
     */
    public function isInteractive()
    {
        // TODO: Implement isInteractive() method.
    }

    /**
     * Sets the input interactivity.
     *
     * @param bool $interactive If the input should be interactive
     */
    public function setInteractive($interactive)
    {
        // TODO: Implement setInteractive() method.
    }

}

class OConsole implements Symfony\Component\Console\Output\OutputInterface
{
    /**
     * Writes a message to the output.
     *
     * @param string|array $messages The message as an array of lines or a single string
     * @param bool $newline Whether to add a newline
     * @param int $type The type of output (one of the OUTPUT constants)
     *
     * @throws \InvalidArgumentException When unknown output type is given
     *
     * @api
     */
    public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL)
    {
        // TODO: Implement write() method.
    }

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string|array $messages The message as an array of lines of a single string
     * @param int $type The type of output (one of the OUTPUT constants)
     *
     * @throws \InvalidArgumentException When unknown output type is given
     *
     * @api
     */
    public function writeln($messages, $type = self::OUTPUT_NORMAL)
    {
        // TODO: Implement writeln() method.
    }

    /**
     * Sets the verbosity of the output.
     *
     * @param int $level The level of verbosity (one of the VERBOSITY constants)
     *
     * @api
     */
    public function setVerbosity($level)
    {
        // TODO: Implement setVerbosity() method.
    }

    /**
     * Gets the current verbosity of the output.
     *
     * @return int The current level of verbosity (one of the VERBOSITY constants)
     *
     * @api
     */
    public function getVerbosity()
    {
        // TODO: Implement getVerbosity() method.
    }

    /**
     * Sets the decorated flag.
     *
     * @param bool $decorated Whether to decorate the messages
     *
     * @api
     */
    public function setDecorated($decorated)
    {
        // TODO: Implement setDecorated() method.
    }

    /**
     * Gets the decorated flag.
     *
     * @return bool true if the output will decorate messages, false otherwise
     *
     * @api
     */
    public function isDecorated()
    {
        // TODO: Implement isDecorated() method.
    }

    /**
     * Sets output formatter.
     *
     * @param \Symfony\Component\Console\Formatter\OutputFormatterInterface $formatter
     *
     * @api
     */
    public function setFormatter(\Symfony\Component\Console\Formatter\OutputFormatterInterface $formatter)
    {
        // TODO: Implement setFormatter() method.
    }

    /**
     * Returns current output formatter instance.
     *
     * @return \Symfony\Component\Console\Formatter\OutputFormatterInterface
     *
     * @api
     */
    public function getFormatter()
    {
        // TODO: Implement getFormatter() method.
    }

}

class LightQueueCmdTest extends TestCase
{
    public function setUp()
    {
        $this->createApplication();
        LightQueueManager::instance()->setDriver('file');
    }

    public function testCmd()
    {
        $fileQueue = new FileQueueProvider(null);

        $fileQueue->push('null');
        $fileQueue->push('null1');
        $fileQueue->push('null2');

        $cmd = new LightQueueCommand();

        $this->setExpectedException('Ark4ne\LightQueue\Exception\LightQueueException', 'LightQueueCommand data invalid');

        $this->assertEquals(0, $cmd->run(new IConsole(), new OConsole()));

        $this->assertEquals(2, $fileQueue->queueSize());

        $this->assertEquals('null1', $fileQueue->next());
        $this->assertEquals(1, $fileQueue->queueSize());

        $this->assertEquals('null2', $fileQueue->next());
        $this->assertFalse($fileQueue->hasNext());

    }
}