<?php

namespace AhmedEbead\WorkflowManager\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateConditionCommand extends Command
{
    protected $signature = 'workflow:condition';
    protected $description = 'Create a new condition for a workflow';

    public function handle()
    {
        $workflowName = $this->ask('Enter the workflow name');
        $conditionName = $this->ask('Enter the condition name');

        $conditionClassPath = base_path("app/Workflows/{$workflowName}/Conditions/{$conditionName}Condition.php");

        if (file_exists($conditionClassPath)) {
            $this->error("Condition '{$conditionName}' already exists.");
            return;
        }

        // Create condition class
        $this->createConditionClass($conditionClassPath, $conditionName, $workflowName);

        // Update config file
        $this->updateConfigFile($workflowName, $conditionName);
        $this->beautifyConfigFile();
        $this->info("Condition '{$conditionName}' created successfully.");
    }
    protected function beautifyConfigFile()
    {
        $configPath = config_path('workflow.php');
        //exec("vendor/bin/phpcbf $configPath");
    }
    protected function createConditionClass($path, $className, $workflowName)
    {
        $classContent = <<<PHP
<?php

namespace App\Workflows\\{$workflowName}\Conditions;

use AhmedEbead\WorkflowManager\Contracts\ConditionInterface;

class {$className}Condition implements ConditionInterface
{
    public function check(\$model)
    {
        return false;
        // Your condition logic here
    }
}
PHP;
        file_put_contents($path, $classContent);
    }

    protected function updateConfigFile($workflowName, $conditionName)
    {
        $configPath = config_path('workflow.php');
        $config = include $configPath;

        if (!isset($config['workflows'][$workflowName])) {
            $config['workflows'][$workflowName] = ['conditions' => []];
        }

        $conditionClass = "App\Workflows\\{$workflowName}\Conditions\\{$conditionName}Condition";

        $config['workflows'][$workflowName]['conditions'][$conditionClass] = [];

        $configContent = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        $configContent = str_replace(['\\\\'], ['\\'], $configContent);
        file_put_contents($configPath, $configContent);
    }
}
