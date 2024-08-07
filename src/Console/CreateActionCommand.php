<?php

namespace AhmedEbead\WorkflowManager\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateActionCommand extends Command
{
    protected $signature = 'workflow:action';
    protected $description = 'Create a new action for a workflow';

    public function handle()
    {
        $workflowName = $this->ask('Enter the workflow name');
        $conditionName = $this->ask('Enter the condition name this action is associated with');
        $actionName = $this->ask('Enter the action name');
        $isQueueable = $this->confirm('Should this action be queueable?', false);

        $actionClassPath = base_path("app/Workflows/{$workflowName}/Actions/{$actionName}Action.php");

        if (file_exists($actionClassPath)) {
            $this->error("Action '{$actionName}' already exists.");
            return;
        }

        // Create action class
        $this->createActionClass($actionClassPath, $actionName, $workflowName, $isQueueable);

        // Update config file
        $this->updateConfigFile($workflowName, $conditionName, $actionName, $isQueueable);
        $this->beautifyConfigFile();
        $this->info("Action '{$actionName}' created successfully.");
    }

    protected function beautifyConfigFile()
    {
        $configPath = config_path('workflow.php');
        //exec("vendor/bin/phpcbf $configPath");
    }

    protected function createActionClass($path, $className, $workflowName, $isQueueable)
    {
        if ($isQueueable) {
            $classContent = <<<PHP
<?php

namespace App\Workflows\\{$workflowName}\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class {$className}Action implements  ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected \$model;

    public function __construct(\$model)
    {
        \$this->model = \$model;
    }

    public function handle()
    {
        try {
            Log::info("Executing {$className}Action for model: " . get_class(\$this->model));
            // Your action logic here
        } catch (Throwable \$e) {
            Log::error('{$className}Action failed.', ['exception' => \$e, 'model' => get_class(\$this->model)]);

            if (\$this->attempts() < config('workflow.retry.attempts')) {
                \$this->release(config('workflow.retry.delay'));
            } else {
                \$this->fail(\$e);
            }
        }
    }
}
PHP;
        } else {
            $classContent = <<<PHP
<?php

namespace App\Workflows\\{$workflowName}\Actions;

use AhmedEbead\WorkflowManager\Contracts\ActionInterface;

class {$className}Action implements ActionInterface
{
    public function execute(\$model)
    {
        // Your action logic here
    }
}
PHP;
        }

        file_put_contents($path, $classContent);
    }

    protected function updateConfigFile($workflowName, $conditionName, $actionName, $isQueueable)
    {
        $configPath = config_path('workflow.php');
        $config = include $configPath;

        if (!isset($config['workflows'][$workflowName]['conditions']["App\Workflows\\{$workflowName}\Conditions\\{$conditionName}Condition"])) {
            $config['workflows'][$workflowName]['conditions']["App\Workflows\\{$workflowName}\Conditions\\{$conditionName}Condition"] = [];
        }

        $actionClass = "App\Workflows\\{$workflowName}\Actions\\{$actionName}Action";
        $config['workflows'][$workflowName]['conditions']["App\Workflows\\{$workflowName}\Conditions\\{$conditionName}Condition"][] = [
            'class' => $actionClass,
            'queueable' => $isQueueable,
        ];

        $configContent = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        $configContent = str_replace(['\\\\'], ['\\'], $configContent);
        file_put_contents($configPath, $configContent);
    }
}
