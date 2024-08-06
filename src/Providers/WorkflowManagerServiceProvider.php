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
        // Register model observers
        $models = config('workflows.models');
        foreach ($models as $modelClass => $workflow) {
            if (class_exists($modelClass)) {
                $modelClass::observe(WorkflowObserver::class);
            }
        }


        // Publish configuration files if needed
        $this->publishes([
            __DIR__.'/../config/workflow.php' => config_path('workflow.php'),
        ]);
    }
}
