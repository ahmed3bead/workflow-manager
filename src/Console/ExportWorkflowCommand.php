<?php

namespace AhmedEbead\WorkflowManager\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportWorkflowCommand extends Command
{
    protected $signature = 'workflow:export';
    protected $description = 'Export a workflow and its conditions/actions as a flowchart';

    public function __construct()
    {
        parent::__construct();
        // Initialize ConvertApi with your API key
    }

    public function handle()
    {
        $workflowName = $this->ask('Enter the workflow name');
        $configPath = config_path('workflow.php');
        $config = include $configPath;

        $workflowPath = base_path("app/Workflows/{$workflowName}");

        if (!isset($config['workflows'][$workflowName])) {
            $this->error("Workflow '{$workflowName}' does not exist in the configuration.");
            return;
        }

        if (file_exists($workflowPath)) {
            $this->info("Exporting workflow '{$workflowName}'...");
            $dotFilePath = $workflowPath . "/{$workflowName}.dot";
            // Generate DOT file content
            // Generate DOT file
            $this->generateDotFile($config['workflows'][$workflowName], $dotFilePath);

            // Optionally convert DOT file to image (if Graphviz is installed)
            $this->convertDotToImage($dotFilePath);

        } else {
            $this->error("Workflow '{$workflowName}' does not exist.");
        }
    }

    protected function generateDotFile(array $workflow, string $path)
    {
        $dotContent = "digraph workflow {\n";
        $dotContent .= "    rankdir=TB;\n"; // Top to Bottom layout
        $dotContent .= "    node [shape=box, style=rounded, fontsize=12, width=2.5, height=1.0];\n"; // Adjust node size
        $dotContent .= "    edge [fontsize=10, len=2.0];\n"; // Adjust edge length

        // Create nodes and edges for each condition and associated actions
        foreach ($workflow['conditions'] as $conditionClass => $actions) {
            // Extract class name for condition
            $conditionName = class_basename($conditionClass);
            $dotContent .= "    \"{$conditionClass}\" [label=\"Condition: {$conditionName}\"];\n";

            foreach ($actions as $actionClass) {
                // Extract class name for action
                $actionName = class_basename($actionClass);
                $dotContent .= "    \"{$conditionClass}\" -> \"{$actionClass}\" [label=\"Action: {$actionName}\"];\n";
                // Add node for the action if not already added
                $dotContent .= "    \"{$actionClass}\" [label=\"Action: {$actionName}\"];\n";
            }
        }

        $dotContent .= "}\n";

        file_put_contents($path, $dotContent);
    }

    protected function convertDotToImagew(string $dotFilePath)
    {
        $pngFilePath = str_replace('.dot', '.png', $dotFilePath);

        // Convert DOT to PNG using Graphviz (if installed)
        if (shell_exec("dot -Tpng {$dotFilePath} -o {$pngFilePath}")) {
            $this->info("Converted DOT file to PNG image.");
        } else {
            $this->warn("Graphviz is not installed or failed to convert DOT to PNG.");
        }
    }

    protected function convertDotToImage(string $dotFilePath)
    {
        $pngFilePath = str_replace('.dot', '.png', $dotFilePath);

        // Convert DOT to PNG using Graphviz (if installed)
        $command = "dot -Tpng {$dotFilePath} -o {$pngFilePath}";
        $output = shell_exec($command);
    }
}
