<?php namespace Hounddd\MallCleaner;

use Backend;
use Backend\Models\UserRole;
use Hounddd\MallCleaner\Classes\Cleaners\EmptyCarts;
use Hounddd\MallCleaner\Classes\Cleaners\OldUnpaidOrders;
use Hounddd\MallCleaner\Console\Cleanup;
use Hounddd\MallCleaner\Console\CleanupV9;
use Illuminate\Foundation\Application as Laravel;
use Illuminate\Support\Facades\Event;
use System\Classes\PluginBase;

/**
 * MallCleaner Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     */
    public function pluginDetails(): array
    {
        return [
            'name'        => 'hounddd.mallcleaner::lang.plugin.name',
            'description' => 'hounddd.mallcleaner::lang.plugin.description',
            'author'      => 'Hounddd',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register(): void
    {
    }

    /**
     * Boot method, called right before the request route.
     */
    public function boot(): void
    {
        $this->registerGdprCleanUp();

        // Register console commands
        if (version_compare(Laravel::VERSION, '9.21.0', '>=')) {
            // Use latest atisan view
            $this->registerConsoleCommand('hounddd.mallcleaner', CleanupV9::class);
        } else {
            // Use default console output
            $this->registerConsoleCommand('hounddd.mallcleaner', Cleanup::class);
        }
    }

    /**
     * Registers any frontend components implemented in this plugin.
     */
    public function registerComponents(): array
    {
        return [];
    }

    /**
     * Registers any backend permissions used by this plugin.
     */
    public function registerPermissions(): array
    {
        return []; // Remove this line to activate

        return [
            'hounddd.mallcleaner.some_permission' => [
                'tab' => 'hounddd.mallcleaner::lang.plugin.name',
                'label' => 'hounddd.mallcleaner::lang.permissions.some_permission',
                'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
            ],
        ];
    }

    /**
     * Registers backend navigation items for this plugin.
     */
    public function registerNavigation(): array
    {
        return [];
    }


    protected function registerGdprCleanUp()
    {
        Event::listen('offline.gdpr::cleanup.register', function () {
            return [
                'id'     => 'wn-mallcleaner-plugin',
                'label'  => 'OFFLINE Mall Cleaner',
                'models' => [
                    [
                        'label'   => 'hounddd.mallcleaner::lang.actions.empty_carts.label',
                        'comment' => 'hounddd.mallcleaner::lang.actions.empty_carts.comment',
                        'class' => EmptyCarts::class,
                    ],
                    [
                        'label'   => 'hounddd.mallcleaner::lang.actions.unpaid_orders.label',
                        'comment' => 'hounddd.mallcleaner::lang.actions.unpaid_orders.comment',
                        'class' => OldUnpaidOrders::class,
                    ],
                    // [
                    //     'id'   => 'wn-mallcleaner-testclosure',
                    //     'label'   => 'Simple test closure',
                    //     'comment' => 'Don't perofm any database changes',
                    //     'closure' => function ($deadline, $keepDays) {
                    //     },
                    // ],
                ],
            ];
        });
    }
}
