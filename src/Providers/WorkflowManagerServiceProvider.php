<?php

namespace AhmedEbead\WorkflowManager\Providers;

use AhmedEbead\WorkflowManager\Services\WorkflowService;
use Illuminate\Support\ServiceProvider;
use AhmedEbead\WorkflowManager\Console\CreateWorkflowCommand;
use AhmedEbead\WorkflowManager\Console\CreateConditionCommand;
use AhmedEbead\WorkflowManager\Console\CreateActionCommand;
use AhmedEbead\WorkflowManager\Console\ExportWorkflowCommand;
use AhmedEbead\WorkflowManager\Observers\WorkflowObserver;
use AhmedEbead\WorkflowManager\Models\Workflow;
use AhmedEbead\WorkflowManager\Models\Condition;
use AhmedEbead\WorkflowManager\Models\Action;

class WorkflowManagerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton(WorkflowService::class, function ($app) {
            return new WorkflowService();
        });

        // Register commands
        $this->commands([
            CreateWorkflowCommand::class,
            CreateConditionCommand::class,
            CreateActionCommand::class,
            ExportWorkflowCommand::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerObservers();



        // Publish configuration files if needed
        $this->publishes([
            __DIR__ . '/../config/workflow.php' => config_path('workflow.php'),
        ], 'config');
    }

    protected function registerObservers()
    {
        // Register model observers
        // Register observers for models defined in the config
        $models = config('workflow.models') ?? [];
        foreach ($models as $modelClass => $workflow) {
            $modelClass::observe(WorkflowObserver::class);
        }
    }
}
