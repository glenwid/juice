#!/usr/bin/env php
<?php
require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;

class UpCommand extends Command
{
    protected static $defaultName = 'up';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        exec('docker images -q bkimminich/juice-shop', $outputLines, $returnVar);
        if (empty($outputLines)) {
            $output->writeln("Docker image not found. Pulling it...");
            exec('docker pull bkimminich/juice-shop');
        }

        exec('docker run -d -p 3000:3000 bkimminich/juice-shop', $outputLines, $returnVar);
        $output->writeln("🧃🦕 slurp juice");

        return Command::SUCCESS;
    }
}

class DownCommand extends Command
{
    protected static $defaultName = 'down';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        exec('docker stop $(docker ps -q --filter ancestor=bkimminich/juice-shop)', $outputLines, $returnVar);
        $output->writeln("juice-shop stopped 💤");

        return Command::SUCCESS;
    }
}

class ToggleCommand extends Command
{
    protected static $defaultName = 'toggle';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Check if the application is running
        exec('docker ps -q --filter ancestor=bkimminich/juice-shop', $outputLines, $returnVar);

        if (empty($outputLines)) {
            // If the application is not running, start it
            $upCommand = new UpCommand();
            $upCommand->execute($input, $output);
        } else {
            // If the application is running, stop it
            $downCommand = new DownCommand();
            $downCommand->execute($input, $output);
        }

        return Command::SUCCESS;
    }
}

$app = new Application();
$app->add(new UpCommand());
$app->add(new DownCommand());
$app->add(new ToggleCommand());  

# Check if any arguments were passed
if ($argc == 1) {
    # If no arguments were passed, default to the 'toggle' command
    $input = new ArrayInput([
        'command' => 'toggle',
    ]);
} else {
    # If arguments were passed, use them as normal
    $input = new ArgvInput();
}

$app->run($input);
