<?php

namespace AhmedEbead\WorkflowManager\Console;

use Illuminate\Console\Command;

class CreateActionCommand extends Command
{
    protected $signature = 'workflow:action';
    protected $description = 'Create a new action for a workflow';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $workflow = $this->ask('Enter the workflow name');
        $name = $this->ask('Enter the action name');

        $path = base_path("app/Workflows/{$workflow}/Actions/{$name}Action.php");

        if (file_exists($path)) {
            $this->error("Action '{$name}' already exists.");
            return;
        }

        $content = "<?php\n\nnamespace App\Workflows\\{$workflow}\Actions;\n\nuse AhmedEbead\WorkflowManager\Contracts\ActionInterface;\n\nclass {$name}Action implements ActionInterface\n{\n    public function execute(\$model)\n    {\n        // Your action logic here\n    }\n}\n";
        file_put_contents($path, $content);

        $this->info("Action '{$name}' created successfully.");
    }
}
