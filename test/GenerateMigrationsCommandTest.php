<?php

namespace Lexide\Reposition\Phinx\Test;
use Lexide\Reposition\Phinx\GenerateMigrationsCommand;
use Lexide\Reposition\Phinx\MigrationGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * GenerateMigrationsCommandTest
 */
class GenerateMigrationsCommandTest extends \PHPUnit_Framework_TestCase
{

    protected $generator;

    protected $output;

    protected $outputInterface;

    protected $inputInterface;

    public function setup()
    {
        $this->generator = \Mockery::mock(MigrationGenerator::class);
        $this->generator->shouldIgnoreMissing(false);

        $this->output = [];

        $this->outputInterface = \Mockery::mock(OutputInterface::class);
        $this->outputInterface->shouldReceive("writeln")->andReturnUsing(function($message) {
            $this->output[] = $message;
            return null;
        });

        $this->inputInterface = \Mockery::mock(InputInterface::class);
        $this->inputInterface->shouldIgnoreMissing(false);

    }

    /**
     * @dataProvider entityProvider
     *
     * @param array $entities
     */
    public function testMigrationsAreGenerated($entities)
    {
        $this->inputInterface->shouldReceive("getArgument")->with("entities")->andReturn($entities);

        $command = new GenerateMigrationsCommand($this->generator);
        $command->run($this->inputInterface, $this->outputInterface);

        $entityCount = count($entities);

        $lastLine = array_pop($this->output);

        $this->assertContains("$entityCount", $lastLine);
    }

    public function entityProvider()
    {
        return [
            [["one"]],
            [["two", "three"]],
            [["one", "two", "three", "four"]]
        ];
    }

}
