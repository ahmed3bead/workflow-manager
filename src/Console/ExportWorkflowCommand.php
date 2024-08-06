<?php

namespace AhmedEbead\WorkflowManager\Console;

use Illuminate\Console\Command;
use File;

class ExportWorkflowCommand extends Command
{
    protected $signature = 'workflow:export';
    protected $description = 'Export a workflow and its conditions/actions as a flowchart';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $workflowName = $this->ask('Enter the workflow name');
        $workflowPath = base_path("app/Workflows/\{$workflowName\}");

        if (file_exists($workflowPath)) {
            $this->info("Exporting workflow '\{$workflowName\}'...");

            // Generate DOT file content
            $dotContent = $this->generateDotFile($workflowPath);
            $dotFilePath = base_path("storage/\{$workflowName\}.dot");
            file_put_contents($dotFilePath, $dotContent);

            // Generate image from DOT file
            $imageFilePath = base_path("storage/\{$workflowName\}.png");
            $this->generateFlowchartImage($dotFilePath, $imageFilePath);

            $this->info("Workflow '\{$workflowName\}' exported successfully as '\{$workflowName\}.png'.");
        } else {
            $this->error("Workflow '\{$workflowName\}' does not exist.");
        }
    }

    protected function generateDotFile($workflowPath)
    {
        $dotContent = "digraph G {\n";
        $dotContent .= "    node [shape=box];\n";

        // Add workflow
        $dotContent .= "    \"Workflow: \{$workflowPath\}\" [shape=ellipse];\n";

        // Add conditions and actions
        $conditionsPath = "\{$workflowPath\}/Conditions";
        if (file_exists($conditionsPath)) {
            foreach (File::files($conditionsPath) as $file) {
                $conditionName = pathinfo($file, PATHINFO_FILENAME);
                $dotContent .= "    \"\{$conditionName\}\" [label=\"Condition: \{$conditionName\}\"];\n";
                $dotContent .= "    \"Workflow: \{$workflowPath\}\" -> \"\{$conditionName\}\";\n";
            }
        }

        $actionsPath = "\{$workflowPath\}/Actions";
        if (file_exists($actionsPath)) {
            foreach (File::files($actionsPath) as $file) {
                $actionName = pathinfo($file, PATHINFO_FILENAME);
                $dotContent .= "    \"\{$actionName\}\" [label=\"Action: \{$actionName\}\"];\n";
                $dotContent .= "    \"Workflow: \{$workflowPath\}\" -> \"\{$actionName\}\";\n";
            }
        }

        $dotContent .= "}\n";

        return $dotContent;
    }

    protected function generateFlowchartImage($dotFilePath, $imageFilePath)
    {
        $command = "dot -Tpng \{$dotFilePath\} -o \{$imageFilePath\}";
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error("Failed to generate image from DOT file.");
        }
    }
}
