<?php

namespace AhmedEbead\WorkflowManager\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class WorkflowService
{
    public function processModel(Model $model)
    {
        $modelClass = get_class($model);
        $workflowName = $this->getWorkflowNameForModel($modelClass);

        if ($workflowName) {
            $this->processWorkflow($workflowName, $model);
        }
    }

    protected function getWorkflowNameForModel($modelClass)
    {
        $config = Config::get('workflow.models', []);
        return $config[$modelClass] ?? null;
    }

    protected function processWorkflow($workflowName, $model)
    {
        $workflows = Config::get("workflow.workflows.{$workflowName}.conditions", []);

        foreach ($workflows as $conditionClass => $actions) {
            $condition = new $conditionClass();
            if ($condition->check($model)) {
                foreach ($actions as $actionClass) {

                    $action = new $actionClass();
                    $action->execute($model);
                }
            }
        }
    }
}
