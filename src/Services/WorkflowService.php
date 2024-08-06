<?php

namespace AhmedEbead\WorkflowManager\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class WorkflowService
{
    /**
     * Process the given model by executing the associated workflow.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function processModel($model)
    {
        $modelClass = get_class($model);
        $workflowId = $this->getWorkflowIdForModel($modelClass);

        if (!$workflowId) {
            Log::info('No workflow found for model: ' . $modelClass);
            return;
        }

        $this->processWorkflow($workflowId, $model);
    }

    /**
     * Get the workflow ID associated with the model class.
     *
     * @param string $modelClass
     * @return string|null
     */
    protected function getWorkflowIdForModel($modelClass)
    {
        $workflowMapping = config('workflows.models');
        return $workflowMapping[$modelClass] ?? null;
    }

    /**
     * Process the workflow by executing conditions and actions.
     *
     * @param string $workflowId
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    protected function processWorkflow($workflowId, $model)
    {
        $workflowPath = base_path('app/workflows/' . $workflowId);

        if (!File::exists($workflowPath)) {
            Log::info('Workflow not found: ' . $workflowId);
            return;
        }

        $conditionsPath = $workflowPath . '/conditions';
        $actionsPath = $workflowPath . '/actions';

        $conditions = File::files($conditionsPath);
        foreach ($conditions as $conditionFile) {
            $conditionClass = 'App\\Conditions\\' . pathinfo($conditionFile, PATHINFO_FILENAME);
            $condition = new $conditionClass();

            if ($condition->check($model)) {
                $actions = File::files($actionsPath);
                foreach ($actions as $actionFile) {
                    $actionClass = 'App\\Actions\\' . pathinfo($actionFile, PATHINFO_FILENAME);
                    $action = new $actionClass();
                    $action->execute($model);
                }
            }
        }
    }
}
