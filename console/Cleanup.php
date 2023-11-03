<?php namespace Hounddd\MallCleaner\Console;

use Carbon\Carbon;
use Hounddd\MallCleaner\Classes\Cleaners\EmptyCarts;
use Hounddd\MallCleaner\Classes\Cleaners\OldUnpaidOrders;
use Hounddd\MallCleaner\Console\CompatibilityCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Winter\Storm\Support\Str;

class Cleanup extends CompatibilityCommand
{
    /**
     * @var string The console command name.
     */
    protected static $defaultName = 'mallcleaner:cleanup';

    // /**
    //  * @var string The name and signature of this command.
    //  */
    // protected $signature = 'mallcleaner:cleanup
    //     {name : The cleanning command to perform. <info>One of all, empty-carts or unpaid-orders</info>}
    //     {--d|days=120 : Number of past days to delete.}
    //     {--dry-run : This will simulate the deletion and show you what would happen.}
    //     {--f|force : Force the operation to run and ignore production warnings and confirmation questions.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Clean OFFLINE.Mall models for unwanted records';

    /**
     * Carbon instance. All data older than this date has to be deleted.
     */
    protected Carbon $deadline;

    /**
     * Number of days that $deadline is in the past
     */
    protected int $keepDays;

    /**
     * Is the command running in dry mode
     */
    protected bool $runningDry;

    /**
     * Tabble to output to the console.
     */
    protected array $outputTable = ['headers' => [], 'lines' => []];



    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $this->info('Running OFFLINE.Mall cleaner.');

        $command = implode(' ', (array) $this->argument('name') ?? $this->ask("What is the user's email?"));
        $method = 'cleanup'. studly_case($command);

        $list = $this->suggestNameValues();

        if (!$this->argument('name')) {
            $message = 'There are no commands defined in the "cleanup" namespace.';
            if (1 == count($list)) {
                $message .= "\n\nDid you mean this?\n    ";
            } else {
                $message .= "\n\nDid you mean one of these?\n    ";
            }

            $message .= implode("\n    ", $list);
            throw new \InvalidArgumentException($message);
        }

        if (!method_exists($this, $method)) {
            $this->error(sprintf('Utility command "%s" does not exist!', $command));
            return;
        }

        $this->keepDays = $this->option('days') ?: 120;
        $this->deadline = Carbon::now()->subDays($this->keepDays);
        $this->runningDry = $this->option('dry-run');

        if ($this->runningDry) {
            $this->warn('Running in dry mode, no records will be deleted');
        }
        $this->newLine();

        if ($command == 'all') {
            $this->outputTable['headers'] = ['<fg=gray;options=bold>Cleanning all</>', '<fg=gray>Count / Status</>'];
        } else {
            $this->outputTable['headers'] = [
                '<fg=gray;options=bold>Cleanning '. Str::replace('-', ' ', $command) .'</>',
                '<fg=gray>Count / Status</>'
            ];
        }

        $this->$method();

        $this->table($this->outputTable['headers'], $this->outputTable['lines']);
        $this->newLine();

    }

    //
    // Cleanups
    //

    /**
     * Run all other cleanup functions
     */
    protected function cleanupAll(): void
    {
        $this->cleanupEmptycarts();
        $this->cleanupUnpaidorders();
    }

    /**
     * Run "Empty carts" cleaner
     */
    protected function cleanupEmptyCarts(): void
    {
        $this->renderActionInfos(EmptyCarts::class, 'Empty carts');
    }

    /**
     * Run "Unpaid orders" cleaner
     */
    protected function cleanupUnpaidOrders(): void
    {
        $this->renderActionInfos(OldUnpaidOrders::class, 'Unpaid orders');
    }


    /**
     * Call cleaner and display result
     */
    protected function renderActionInfos(string $class, string $name): void
    {
        $count = (new $class)->gdprCleanup($this->deadline, $this->keepDays, $this->runningDry);

        $action = '<options=bold>['. $count . '] <fg=green>Deleted</></>';
        if ($this->runningDry) {
            $action = '<options=bold>['. $count . '] <fg=yellow>To delete</></>';
        }

        $this->outputTable['lines'][] = ['<fg=yellow>'. $name .'</>', $action];
    }


    /**
     * Provide autocomplete suggestions for the "name" argument
     */
    public function suggestNameValues(): array
    {
        $methods = preg_grep('/^cleanup/', get_class_methods(get_called_class()));
        return array_map(function ($item) {
            $item = Str::replace('cleanup', '', $item);
            return "mallcleaner:cleanup ". snake_case($item, "-");
        }, $methods);
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'name',
                InputArgument::IS_ARRAY,
                'The cleanning command to perform. <info>One of all, empty-carts or unpaid-orders</info>'
            ],
        ];
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'days',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Number of past days to delete.',
                120,
            ],
            [
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'This will simulate the deletion and show you what would happen.',
            ],
        ];
    }
}
