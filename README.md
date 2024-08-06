# AhmedEbead\\WorkflowManager Documentation

## Overview

`AhmedEbead\WorkflowManager` is a Laravel package for managing workflows. It supports creating, managing, and executing workflows with conditions and actions. The package allows dynamic interaction through user-defined classes and integrates seamlessly with Laravel's model lifecycle.

## Table of Contents

*   [Installation](#installation)
*   [Configuration](#configuration)
*   [Creating Workflows](#creating-workflows)
*   [Managing Conditions](#managing-conditions)
*   [Defining Actions](#defining-actions)
*   [Usage](#usage)
*   [Commands](#commands)
*   [Exporting Workflows](#exporting-workflows)
*   [Observing Models](#observing-models)

## Installation

1.  **Add the Package**: Add the package to your Laravel application via Composer.

    ```
    composer require ahmedebead/workflow-manager
    ```

2.  **Publish the Configuration**: Publish the package configuration file.

    ```
    php artisan vendor:publish --provider="AhmedEbead\WorkflowManager\Providers\WorkflowManagerServiceProvider"
    ```


## Configuration

1.  **Add Models to Configuration**: Configure which models use workflows in `config/workflows.php`.

    ```
    return [
        'models' => [
            \App\Models\Order::class => 'order_workflow',
            \App\Models\User::class => 'user_workflow',
            \App\Models\Product::class => 'product_workflow',
        ],
    ];
    ```


## Creating Workflows

To create a new workflow, use the `workflow:create` command. This command will prompt you for details about the workflow, including its name.

```
php artisan workflow:create
```

This command will create a new directory within the `app/Workflows` directory. For example, if you create a workflow named "OrderWorkflow", it will be placed in `app/Workflows/OrderWorkflow`.

Ensure that the workflow name you provide matches the name specified in the configuration file `config/workflows.php`. For instance, if you configure `\App\Models\Order::class => 'order_workflow'`, the workflow directory should be named `order_workflow`.

## Managing Conditions

1.  **Create a Condition**: Use the `workflow:condition` command to create a new condition.

    ```
    php artisan workflow:condition
    ```

    You will be prompted to enter the condition name and the class name. Conditions will be created in the `app/Workflows/{workflow_name}/Conditions` directory.

2.  **Define the Condition Class**: Add your condition logic in the newly created class within the `app/Workflows/{workflow_name}/Conditions` directory.

## Defining Actions

1.  **Create an Action**: Use the `workflow:action` command to create a new action.

    ```
    php artisan workflow:action
    ```

    You will be prompted to enter the action name and the class name. Actions will be created in the `app/Workflows/{workflow_name}/Actions` directory.

2.  **Define the Action Class**: Add your action logic in the newly created class within the `app/Workflows/{workflow_name}/Actions` directory.

## Usage

1.  **Create and Configure Models**: Create your models and ensure they are listed in the `config/workflows.php` file.
2.  **Define Workflows**: Define workflows by creating workflow files and classes in the `app/Workflows` directory.
3.  **Attach Workflows to Models**: Ensure models use workflows as configured in the `config/workflows.php`.
4.  **Handle Conditions and Actions**: Define conditions and actions as user-defined classes and place them in the appropriate directories under `app/Workflows/{workflow_name}`.

## Commands

**Create Workflow**

```
php artisan workflow:create
```

Prompts for the workflow name. Creates a new directory in `app/Workflows` with the specified workflow name. Ensure this name matches the entry in `config/workflows.php`.

**Create Condition**

```
php artisan workflow:condition
```

Prompts for the condition name and class. Creates a new condition class in `app/Workflows/{workflow_name}/Conditions`.

**Create Action**

```
php artisan workflow:action
```

Prompts for the action name and class. Creates a new action class in `app/Workflows/{workflow_name}/Actions`.

**Export Workflow**

```
php artisan workflow:export
```

Exports workflows and their related conditions and actions as a flowchart. This feature requires Graphviz to be installed on your system.

## Exporting Workflows

The `workflow:export` command will export the workflow, conditions, and actions as a flowchart. Ensure Graphviz is installed to enable this functionality.

## Observing Models

To observe models and trigger workflows, ensure the models are listed in the `config/workflows.php` file. The `WorkflowObserver` will process the workflow when models are saved or updated.
