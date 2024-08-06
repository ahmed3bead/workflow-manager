<?php

namespace AhmedEbead\WorkflowManager\Console;

use Illuminate\Console\Command;

class CreateWorkflowCommand extends Command
{
    protected $signature = 'workflow:create';
    protected $description = 'Create a new workflow';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $name = $this->ask('Enter the workflow name');
        $path = base_path("app/Workflows/{$name}");

        if (file_exists($path)) {
            $this->error("Workflow '{$name}' already exists.");
            return;
        }

        mkdir($path, 0755, true);
        mkdir($path."/Conditions", 0755, true);
        mkdir($path."/Actions", 0755, true);

        $this->info("Workflow '{$name}' created successfully.");
    }
}
