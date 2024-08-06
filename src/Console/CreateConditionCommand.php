<?php

namespace AhmedEbead\WorkflowManager\Console;

use Illuminate\Console\Command;

class CreateConditionCommand extends Command
{
    protected $signature = 'workflow:condition';
    protected $description = 'Create a new condition for a workflow';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $workflow = $this->ask('Enter the workflow name');
        $name = $this->ask('Enter the condition name');
        $path = base_path("app/Workflows/{$workflow}/Conditions/{$name}Condition.php");

        if (file_exists($path)) {
            $this->error("Condition '{$name}' already exists.");
            return;
        }

        $content = "<?php\n\nnamespace App\Workflows\\{$workflow}\Conditions;\n\nuse AhmedEbead\WorkflowManager\Contracts\ConditionInterface;\n\nclass {$name}Condition implements ConditionInterface\n{\n    public function check(\$model)\n    {\n        // Your condition logic here\n    }\n}\n";
        file_put_contents($path, $content);

        $this->info("Condition '{$name}' created successfully.");
    }
}
