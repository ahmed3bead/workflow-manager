![Workflow Manager Logo](https://raw.githubusercontent.com/ahmed3bead/workflow-manager/main/src/210139313-43f0d7ed-2005-4b71-9149-540f124c2c2f.png)

# Workflow Manager Documentation

*   [Introduction](#introduction)
*   [Installation](#installation)
*   [Configuration](#configuration)
*   [Commands](#commands)
*   [Features](#features)
*   [Contributing](#contributing)
*   [Changelog](#changelog)
*   [License](#license)
*   [Contact](#contact)

## Introduction

**Workflow Manager** is a powerful Laravel package designed to streamline and automate workflow management within your Laravel application. Whether you're managing complex business processes or simple task sequences, this package provides a robust and flexible solution.

With **Workflow Manager**, you can:

*   **Define Workflows:** Easily set up workflows that model your application's processes, linking various conditions and actions.
*   **Create and Manage Conditions:** Define custom conditions that control when actions should be executed, providing fine-grained control over your workflows.
*   **Define and Execute Actions:** Implement actions that are triggered by specific conditions, automating repetitive tasks and ensuring smooth process execution.
*   **Queueable Actions:** Optionally make actions queueable to enhance performance and reliability.
*   **Visualize Workflows:** Export your workflows to flowcharts, providing a clear, visual representation of your processes.

The package integrates seamlessly with Laravel's existing features, allowing you to leverage its power without adding unnecessary complexity to your application. By using **Workflow Manager**, you can enhance the maintainability, scalability, and efficiency of your application’s workflows.

Get started quickly with simple commands and intuitive configuration, and explore advanced features as needed to fit your specific use cases.

## Installation

To install Workflow Manager, use Composer:

```
composer require ahmedebead/workflow-manager
```

## Configuration

Publish the configuration file with the following Artisan command:

```bash
php artisan vendor:publish --tag=config
```

This will create a `config/workflow.php` file where you can define your workflows, conditions, and actions.

## Commands

### Create Workflow

Generates a new workflow and updates the configuration file and Create the necessary folders for the workflow classes in app/Workflows.


**Usage:**

```bash
php artisan workflow:create
```

**Example:**

```
Enter the workflow name: OrderWorkflow
Enter the model class for this workflow (e.g., \App\Models\User): \App\Models\Order
Workflow 'OrderWorkflow' created successfully.
```

### Create Condition

Creates a new condition class for a workflow.

**Usage:**

```bash
php artisan workflow:condition
```

**Example:**

```
Enter the workflow name: OrderWorkflow
Enter the condition name: IsPending
Condition 'IsPending' created successfully.
```
***Example condition:***
```php
<?php

namespace App\Workflows\Users\Conditions;

use AhmedEbead\WorkflowManager\Contracts\ConditionInterface;

class UserActivatedCondition implements ConditionInterface
{
    public function check($model)
    {
        // Your Condition logic here
        // return $model->status == 'active';
    }
}
```

### Create Action

Creates a new action class associated with a condition. You can choose to make the action queueable or not.

**Usage:**

```bash
php artisan workflow:action
```

**Example:**

```
Enter the workflow name: OrderWorkflow
Enter the condition name this action is associated with: IsPending
Enter the action name: SendEmail
Should this action be queueable? (yes/no): yes
Action 'SendEmail' created successfully with queueable support.
```
***Example queueable action:***
```php
<?php

namespace App\Workflows\Order\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReserveStockAction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function handle()
    {
        try {
            Log::info("Executing ReserveStockAction for model: " . get_class($this->model));
            // Your action logic here
        } catch (Throwable $e) {
            Log::error('ReserveStockAction failed.', ['exception' => $e, 'model' => get_class($this->model)]);

            if ($this->attempts() < config('workflow.retry.attempts')) {
                $this->release(config('workflow.retry.delay'));
            } else {
                $this->fail($e);
            }
        }
    }
}
```

***Example normal action:***
```php
<?php

namespace App\Workflows\Users\Actions;

use AhmedEbead\WorkflowManager\Contracts\ActionInterface;

class ReserveStockAction implements ActionInterface
{
    public function execute($model)
    {
        // Your action logic here
    }
}
```

### Export Workflow

Export a workflow and its conditions/actions as a flowchart:

```bash
php artisan workflow:export
```

**Prompts:**

*   Enter the workflow name.

**Details:**

1.  **Generate DOT File**  
    The command generates a DOT file representing the workflow's conditions and actions. The DOT file is created at `app/Workflows/{workflowName}/{workflowName}.dot`.
2.  **Convert DOT to PNG (Optional)**  
    If Graphviz is installed on your system, the DOT file is converted to a PNG image for easier visualization. The PNG file is saved in the same directory as the DOT file.

**Important:** To convert the DOT file to a PNG image, you must have Graphviz installed. If Graphviz is not installed or the conversion fails, you'll see a warning message.

#### Installing Graphviz

Follow these instructions to install Graphviz:

*   **Ubuntu/Debian:**

    ```
    sudo apt-get install graphviz
            
    ```

*   **macOS:**

    ```
    brew install graphviz
            
    ```

*   **Windows:**

    ```
    Download and install Graphviz from the Graphviz website.
            
    ```


## Features

*   **Dynamic Workflow Management:** Create and manage workflows, conditions, and actions via Artisan commands.
*   **Custom Conditions and Actions:** Implement custom logic for conditions and actions.
*   **Queueable Actions:** Support for queueable actions with advanced features like batch processing, rollback on failure, and transaction management.
*   **Automated Processing:** Automatically process models based on workflows.
*   **Configuration Management:** Easily update and manage workflows through a configuration file.

## Contributing

We welcome contributions to Workflow Manager! To contribute:

1.  Fork the repository.
2.  Create a new branch for your changes.
3.  Commit your changes and push to your fork.
4.  Open a pull request describing your changes.

For detailed contribution guidelines, see the [CONTRIBUTING.md](CONTRIBUTING.md) file.

## Changelog

For a list of changes and updates, refer to the [CHANGELOG.md](CHANGELOG.md) file.

## License

Workflow Manager is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.

## Contact

For questions or support, please contact [ahmed3bead](https://github.com/ahmed3bead) or open an issue on GitHub.
