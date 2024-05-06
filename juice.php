#!/usr/bin/env php
<?php
require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpCommand extends Command
{
    protected static $defaultName = 'app:up';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        exec('docker images -q bkimminich/juice-shop', $outputLines, $returnVar);
        if (empty($outputLines)) {
            $output->writeln("Docker image not found. Pulling it...");
            exec('docker pull bkimminich/juice-shop');
        }

        exec('docker run -d -p 3000:3000 bkimminich/juice-shop', $outputLines, $returnVar);
        $output->writeln($outputLines);
        return Command::SUCCESS;
    }
}

class DownCommand extends Command
{
    protected static $defaultName = 'app:down';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        exec('docker stop $(docker ps -q --filter ancestor=bkimminich/juice-shop)', $outputLines, $returnVar);
        $output->writeln($outputLines);
        return Command::SUCCESS;
    }
}

$app = new Application();
$app->add(new UpCommand());
$app->add(new DownCommand());
$app->run();