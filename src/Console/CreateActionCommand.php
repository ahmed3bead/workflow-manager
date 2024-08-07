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

        $actionClassPath = base_path("app/Workflows/{$workflowName}/Actions/{$actionName}Action.php");

        if (file_exists($actionClassPath)) {
            $this->error("Action '{$actionName}' already exists.");
            return;
        }

        // Create action class
        $this->createActionClass($actionClassPath, $actionName, $workflowName);

        // Update config file
        $this->updateConfigFile($workflowName, $conditionName, $actionName);
        $this->beautifyConfigFile();
        $this->info("Action '{$actionName}' created successfully.");
    }
    protected function beautifyConfigFile()
    {
        $configPath = config_path('workflow.php');
        //exec("vendor/bin/phpcbf $configPath");
    }
    protected function createActionClass($path, $className, $workflowName)
    {
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
        file_put_contents($path, $classContent);
    }

    protected function updateConfigFile($workflowName, $conditionName, $actionName)
    {
        $configPath = config_path('workflow.php');
        $config = include $configPath;

        if (!isset($config['workflows'][$workflowName]['conditions']["App\Workflows\\{$workflowName}\Conditions\\{$conditionName}Condition"])) {
            $config['workflows'][$workflowName]['conditions']["App\Workflows\\{$workflowName}\Conditions\\{$conditionName}Condition"] = [];
        }

        $actionClass = "App\Workflows\\{$workflowName}\Actions\\{$actionName}Action";
        $config['workflows'][$workflowName]['conditions']["App\Workflows\\{$workflowName}\Conditions\\{$conditionName}Condition"][] = $actionClass;

        $configContent = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        file_put_contents($configPath, $configContent);
    }
}
