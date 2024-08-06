#!/bin/bash

# Base directory for package
PACKAGE_DIR="src"

# Create package directories
mkdir -p $PACKAGE_DIR/Commands
mkdir -p $PACKAGE_DIR/Models
mkdir -p $PACKAGE_DIR/Contracts
mkdir -p $PACKAGE_DIR/Observers
mkdir -p $PACKAGE_DIR/Providers

# Create CreateWorkflowCommand.php
cat <<EOL > $PACKAGE_DIR/Commands/CreateWorkflowCommand.php
<?php

namespace AhmedEbead\WorkflowManager\Commands;

use Illuminate\Console\Command;

class CreateWorkflowCommand extends Command
{
    protected \$signature = 'workflow:create';
    protected \$description = 'Create a new workflow';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        \$name = \$this->ask('Enter the workflow name');
        \$path = base_path("app/Workflows/\{\$name\}");

        if (file_exists(\$path)) {
            \$this->error("Workflow '\{\$name\}' already exists.");
            return;
        }

        mkdir(\$path, 0755, true);
        mkdir("{$path}/Conditions", 0755, true);
        mkdir("{$path}/Actions", 0755, true);

        \$this->info("Workflow '\{\$name\}' created successfully.");
    }
}
EOL

# Create CreateConditionCommand.php
cat <<EOL > $PACKAGE_DIR/Commands/CreateConditionCommand.php
<?php

namespace AhmedEbead\WorkflowManager\Commands;

use Illuminate\Console\Command;

class CreateConditionCommand extends Command
{
    protected \$signature = 'workflow:condition';
    protected \$description = 'Create a new condition for a workflow';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        \$name = \$this->ask('Enter the condition name');
        \$workflow = \$this->ask('Enter the workflow name');
        \$path = base_path("app/Workflows/\{\$workflow\}/Conditions/\{\$name\}Condition.php");

        if (file_exists(\$path)) {
            \$this->error("Condition '\{\$name\}' already exists.");
            return;
        }

        \$content = "<?php\n\nnamespace App\\Workflows\\\{\$workflow\}\\Conditions;\n\nuse AhmedEbead\\WorkflowManager\\Contracts\\ConditionInterface;\n\nclass \{\$name\}Condition implements ConditionInterface\n{\n    public function check(\$model)\n    {\n        // Your condition logic here\n    }\n}\n";
        file_put_contents(\$path, \$content);

        \$this->info("Condition '\{\$name\}' created successfully.");
    }
}
EOL

# Create CreateActionCommand.php
cat <<EOL > $PACKAGE_DIR/Commands/CreateActionCommand.php
<?php

namespace AhmedEbead\WorkflowManager\Commands;

use Illuminate\Console\Command;

class CreateActionCommand extends Command
{
    protected \$signature = 'workflow:action';
    protected \$description = 'Create a new action for a workflow';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        \$name = \$this->ask('Enter the action name');
        \$workflow = \$this->ask('Enter the workflow name');
        \$path = base_path("app/Workflows/\{\$workflow\}/Actions/\{\$name\}Action.php");

        if (file_exists(\$path)) {
            \$this->error("Action '\{\$name\}' already exists.");
            return;
        }

        \$content = "<?php\n\nnamespace App\\Workflows\\\{\$workflow\}\\Actions;\n\nuse AhmedEbead\\WorkflowManager\\Contracts\\ActionInterface;\n\nclass \{\$name\}Action implements ActionInterface\n{\n    public function execute(\$model)\n    {\n        // Your action logic here\n    }\n}\n";
        file_put_contents(\$path, \$content);

        \$this->info("Action '\{\$name\}' created successfully.");
    }
}
EOL

# Create ExportWorkflowCommand.php
cat <<EOL > $PACKAGE_DIR/Commands/ExportWorkflowCommand.php
<?php

namespace AhmedEbead\WorkflowManager\Commands;

use Illuminate\Console\Command;
use File;

class ExportWorkflowCommand extends Command
{
    protected \$signature = 'workflow:export';
    protected \$description = 'Export a workflow and its conditions/actions as a flowchart';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        \$workflowName = \$this->ask('Enter the workflow name');
        \$workflowPath = base_path("app/Workflows/\{\$workflowName\}");

        if (file_exists(\$workflowPath)) {
            \$this->info("Exporting workflow '\{\$workflowName\}'...");

            // Generate DOT file content
            \$dotContent = \$this->generateDotFile(\$workflowPath);
            \$dotFilePath = base_path("storage/\{\$workflowName\}.dot");
            file_put_contents(\$dotFilePath, \$dotContent);

            // Generate image from DOT file
            \$imageFilePath = base_path("storage/\{\$workflowName\}.png");
            \$this->generateFlowchartImage(\$dotFilePath, \$imageFilePath);

            \$this->info("Workflow '\{\$workflowName\}' exported successfully as '\{\$workflowName\}.png'.");
        } else {
            \$this->error("Workflow '\{\$workflowName\}' does not exist.");
        }
    }

    protected function generateDotFile(\$workflowPath)
    {
        \$dotContent = "digraph G {\n";
        \$dotContent .= "    node [shape=box];\n";

        // Add workflow
        \$dotContent .= "    \"Workflow: \{\$workflowPath\}\" [shape=ellipse];\n";

        // Add conditions and actions
        \$conditionsPath = "\{\$workflowPath\}/Conditions";
        if (file_exists(\$conditionsPath)) {
            foreach (File::files(\$conditionsPath) as \$file) {
                \$conditionName = pathinfo(\$file, PATHINFO_FILENAME);
                \$dotContent .= "    \"\{\$conditionName\}\" [label=\"Condition: \{\$conditionName\}\"];\n";
                \$dotContent .= "    \"Workflow: \{\$workflowPath\}\" -> \"\{\$conditionName\}\";\n";
            }
        }

        \$actionsPath = "\{\$workflowPath\}/Actions";
        if (file_exists(\$actionsPath)) {
            foreach (File::files(\$actionsPath) as \$file) {
                \$actionName = pathinfo(\$file, PATHINFO_FILENAME);
                \$dotContent .= "    \"\{\$actionName\}\" [label=\"Action: \{\$actionName\}\"];\n";
                \$dotContent .= "    \"Workflow: \{\$workflowPath\}\" -> \"\{\$actionName\}\";\n";
            }
        }

        \$dotContent .= "}\n";

        return \$dotContent;
    }

    protected function generateFlowchartImage(\$dotFilePath, \$imageFilePath)
    {
        \$command = "dot -Tpng \{\$dotFilePath\} -o \{\$imageFilePath\}";
        exec(\$command, \$output, \$returnVar);

        if (\$returnVar !== 0) {
            \$this->error("Failed to generate image from DOT file.");
        }
    }
}
EOL

# Create Workflow.php
cat <<EOL > $PACKAGE_DIR/Models/Workflow.php
<?php

namespace AhmedEbead\WorkflowManager\Models;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    protected \$fillable = ['name'];
}
EOL

# Create Condition.php
cat <<EOL > $PACKAGE_DIR/Models/Condition.php
<?php

namespace AhmedEbead\WorkflowManager\Models;

use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    protected \$fillable = ['name', 'condition_class'];
}
EOL

# Create Action.php
cat <<EOL > $PACKAGE_DIR/Models/Action.php
<?php

namespace AhmedEbead\WorkflowManager\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected \$fillable = ['name', 'condition_id', 'action_class'];
}
EOL

# Create ConditionInterface.php
cat <<EOL > $PACKAGE_DIR/Contracts/ConditionInterface.php
<?php

namespace AhmedEbead\WorkflowManager\Contracts;

interface ConditionInterface
{
    public function check(\$model);
}
EOL

# Create ActionInterface.php
cat <<EOL > $PACKAGE_DIR/Contracts/ActionInterface.php
<?php

namespace AhmedEbead\WorkflowManager\Contracts;

interface ActionInterface
{
    public function execute(\$model);
}
EOL

# Create WorkflowObserver.php
cat <<EOL > $PACKAGE_DIR/Observers/WorkflowObserver.php
<?php

namespace AhmedEbead\WorkflowManager\Observers;

use Illuminate\Support\Facades\Log;
use AhmedEbead\WorkflowManager\Models\Condition;
use AhmedEbead\WorkflowManager\Models\Action;

class WorkflowObserver
{
    public function saved(\$model)
    {
        // Check conditions and execute actions
        \$this->checkAndExecute(\$model);
    }

    protected function checkAndExecute(\$model)
    {
        \$workflows = \AhmedEbead\WorkflowManager\Models\Workflow::all();
        foreach (\$workflows as \$workflow) {
            \$conditions = Condition::where('workflow_id', \$workflow->id)->get();
            foreach (\$conditions as \$condition) {
                \$conditionInstance = app(\$condition->condition_class);
                if (\$conditionInstance->check(\$model)) {
                    \$actions = Action::where('condition_id', \$condition->id)->get();
                    foreach (\$actions as \$action) {
                        \$actionInstance = app(\$action->action_class);
                        \$actionInstance->execute(\$model);
                    }
                }
            }
        }
    }
}
EOL

# Create WorkflowManagerServiceProvider.php
cat <<EOL > $PACKAGE_DIR/Providers/WorkflowManagerServiceProvider.php
<?php

namespace AhmedEbead\WorkflowManager\Providers;

use Illuminate\Support\ServiceProvider;
use AhmedEbead\WorkflowManager\Observers\WorkflowObserver;
use AhmedEbead\WorkflowManager\Models\Workflow;
use AhmedEbead\WorkflowManager\Models\Condition;
use AhmedEbead\WorkflowManager\Models\Action;

class WorkflowManagerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register observers for models
        Workflow::observe(WorkflowObserver::class);
        Condition::observe(WorkflowObserver::class);
        Action::observe(WorkflowObserver::class);
    }

    public function register()
    {
        // Register any package services
    }
}
EOL

echo "Package files created successfully."
