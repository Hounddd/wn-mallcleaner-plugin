<?php

namespace Hounddd\MallCleaner\Console;

use Illuminate\Foundation\Application as Laravel;
use Winter\Storm\Console\Command;

class CompatibilityCommand extends Command
{
    protected bool $beautyMode = false;

    public function __construct()
    {
        parent::__construct();

        // if (version_compare(Laravel::VERSION, '9.21.0', '>=')) {
        //     $this->beautyMode = true;
        // }
        if (interface_exists('\Illuminate\Contracts\Console\PromptsForMissingInput')) {
            $this->beautyMode = true;
        }
    }


    /**
     * Write a string as information output.
     *
     * @param  string  $string
     * @param  int|string|null  $verbosity
     * @return void
     */
    public function info($string, $verbosity = null)
    {
        if ($this->beautyMode) {
            $this->components->info($string, $this->parseVerbosity($verbosity));
        } else {
            parent::info($string, $verbosity);
        }
    }

    /**
     * Write a string as warning output.
     *
     * @param  string  $string
     * @param  int|string|null  $verbosity
     * @return void
     */
    public function warn($string, $verbosity = null)
    {
        if ($this->beautyMode) {
            $this->components->warn($string, $this->parseVerbosity($verbosity));
        } else {
            parent::warn($string, $verbosity);
        }
    }

    /**
     * Format input to textual table.
     *
     * @param  array  $headers
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $rows
     * @param  \Symfony\Component\Console\Helper\TableStyle|string  $tableStyle
     * @param  array  $columnStyles
     * @return void
     */
    public function table($headers, $rows, $tableStyle = 'default', array $columnStyles = [])
    {
        if ($this->beautyMode) {
            $this->components->twoColumnDetail(
                $this->outputTable['headers'][0] ?? '',
                $this->outputTable['headers'][1] ?? ''
            );

            foreach ($this->outputTable['lines'] as $line) {
                $this->components->twoColumnDetail(
                    $line[0] ?? '',
                    $line[1] ?? ''
                );
            }
        } else {
            parent::table($headers, $rows, $tableStyle, $columnStyles);
        }
    }
}
