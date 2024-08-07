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

Workflow Manager facilitates the creation and management of workflows in Laravel applications. It integrates with your Eloquent models and provides a flexible system for defining conditions and actions.

## Installation

To install Workflow Manager, use Composer:

```
composer require ahmedebead/workflow-manager
```

## Configuration

Publish the configuration file with the following Artisan command:

```
php artisan vendor:publish --tag=config
```

This will create a `config/workflow.php` file where you can define your workflows, conditions, and actions.

## Commands

### Create Workflow

Generates a new workflow and updates the configuration file.

**Usage:**

```
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

```
php artisan workflow:condition
```

**Example:**

```
Enter the workflow name: OrderWorkflow
Enter the condition name: IsPending
Condition 'IsPending' created successfully.
```

### Create Action

Creates a new action class associated with a condition.

**Usage:**

```
php artisan workflow:action
```

**Example:**

```
Enter the workflow name: OrderWorkflow
Enter the condition name this action is associated with: IsPending
Enter the action name: SendEmail
Action 'SendEmail' created successfully.
```

### Export Workflow

Export a workflow and its conditions/actions as a flowchart:

```
php artisan workflow:export
```

**Prompts:**

*   Enter the workflow name.

**Details:**

1.  **Generate DOT File**

The command generates a DOT file representing the workflow's conditions and actions. The DOT file is created at `app/Workflows/{workflowName}/{workflowName}.dot`.

3.  **Convert DOT to PNG (Optional)**

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

Download and install Graphviz from the [Graphviz website](https://graphviz.gitlab.io/download/).

## Features

*   **Dynamic Workflow Management:** Create and manage workflows, conditions, and actions via Artisan commands.
*   **Custom Conditions and Actions:** Implement custom logic for conditions and actions.
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
