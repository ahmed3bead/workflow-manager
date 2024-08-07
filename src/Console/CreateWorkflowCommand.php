<?php

namespace AhmedEbead\WorkflowManager\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateWorkflowCommand extends Command
{
    protected $signature = 'workflow:create';
    protected $description = 'Create a new workflow';

    public function handle()
    {
        $workflowName = $this->ask('Enter the workflow name');
        $modelClass = $this->ask('Enter the model class for this workflow (e.g., \App\Models\User)');

        $workflowPath = base_path("app/Workflows/{$workflowName}");

//        if (file_exists($workflowPath)) {
//            $this->error("Workflow '{$workflowName}' already exists.");
//            return;
//        }
//
//        // Create directories
//        File::makeDirectory($workflowPath, 0755, true);
//        File::makeDirectory("{$workflowPath}/Conditions", 0755, true);
//        File::makeDirectory("{$workflowPath}/Actions", 0755, true);

        // Update config file
        $this->updateConfigFile($modelClass, $workflowName);
        $this->beautifyConfigFile();
        $this->info("Workflow '{$workflowName}' created successfully.");
    }
    protected function beautifyConfigFile()
    {
        $configPath = config_path('workflow.php');
        exec("vendor/bin/phpcbf $configPath");
    }
    protected function updateConfigFile($modelClass, $workflowName)
    {
        $configPath = config_path('workflow.php');
        $config = include $configPath;

        // Normalize the model class path
        $modelClass = ltrim($modelClass, '\\');

        // Update configuration
        $config['models'][$modelClass] = $workflowName;
        // Write to file
        $configContent = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        $configContent = str_replace(['\\\\'], ['\\'], $configContent);
        file_put_contents($configPath, $configContent);
    }
}
