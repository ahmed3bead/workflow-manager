<?php
namespace AhmedEbead\WorkflowManager\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use \GraphViz\Graph;

class ExportWorkflowCommand extends Command
{
    protected $signature = 'workflow:export {workflowName}';
    protected $description = 'Export a workflow and its conditions/actions as a flowchart';

    public function handle()
    {
        $workflowName = $this->argument('workflowName');
        $configPath = config_path('workflow.php');
        $config = include $configPath;

        if (!isset($config['workflows'][$workflowName])) {
            $this->error("Workflow '{$workflowName}' does not exist in the configuration.");
            return;
        }

        $workflowPath = base_path("app/Workflows/{$workflowName}");

        if (!File::isDirectory($workflowPath)) {
            $this->error("Workflow directory '{$workflowPath}' does not exist.");
            return;
        }

        $this->info("Exporting workflow '{$workflowName}'...");
        $dotFilePath = $workflowPath . "/{$workflowName}.dot";
        $pngFilePath = $workflowPath . "/{$workflowName}.png";

        try {
            $this->generateDotFile($config['workflows'][$workflowName], $dotFilePath);
            $this->convertDotToImage($dotFilePath, $pngFilePath);
            $this->info("Workflow exported successfully to {$pngFilePath}");
        } catch (\Exception $e) {
            $this->error("An error occurred while exporting the workflow: {$e->getMessage()}");
        }
    }

    protected function generateDotFile(array $workflow, string $path)
    {
        $graph = new Graph('workflow');
        $graph->setAttribute('rankdir', 'TB');
        $graph->setAttribute('node', ['shape' => 'box', 'style' => 'rounded', 'fontsize' => 12, 'width' => 2.5, 'height' => 1.0]);
        $graph->setAttribute('edge', ['fontsize' => 10, 'len' => 2.0]);

        foreach ($workflow['conditions'] as $conditionClass => $actions) {
            $conditionName = class_basename($conditionClass);
            $conditionNode = $graph->addNode($conditionClass, ['label' => "Condition: {$conditionName}"]);

            foreach ($actions as $actionClass) {
                $actionName = class_basename($actionClass);
                $actionNode = $graph->addNode($actionClass, ['label' => "Action: {$actionName}"]);
                $graph->addEdge($conditionNode, $actionNode);
            }
        }

        $graph->output($path);
    }

    protected function convertDotToImage(string $dotFilePath, string $pngFilePath)
    {
        // Use GraphViz library to convert DOT to PNG
        $graph = new Graph();
        $graph->read($dotFilePath);
        $graph->output($pngFilePath, 'png');
    }
}
