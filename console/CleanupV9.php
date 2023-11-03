<?php namespace Hounddd\MallCleaner\Console;

use Hounddd\MallCleaner\Console\Cleanup;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\suggest;


class CleanupV9 extends Cleanup implements PromptsForMissingInput
{
    /**
     * @var string The console command name.
     */
    protected static $defaultName = 'mallcleaner:cleanup';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'mallcleaner:cleanup
        {name : The cleanning command to perform. <info>One of all, empty-carts or unpaid-orders</info>}
        {--d|days=120 : Number of past days to delete.}
        {--dry-run : This will simulate the deletion and show you what would happen.}
        {--f|force : Force the operation to run and ignore production warnings and confirmation questions.}';
}
