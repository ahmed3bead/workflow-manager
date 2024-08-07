<?php

namespace AhmedEbead\WorkflowManager\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $retryConfig = Config::get('workflow.retry', ['attempts' => 3, 'delay' => 30]);

        DB::beginTransaction();

        try {
            foreach ($workflows as $conditionClass => $actions) {
                $condition = new $conditionClass();
                if ($condition->check($model)) {
                    $jobs = [];

                    foreach ($actions as $action) {
                        $actionClass = $action['class'];
                        $queueable = $action['queueable'] ?? false;

                        if ($queueable) {
                            $jobs[] = new $actionClass($model);
                        } else {
                            $actionInstance = new $actionClass();
                            $actionInstance->execute($model);
                        }
                    }

                    if (!empty($jobs)) {
                        // Dispatch batch with failure handling
                        Bus::batch($jobs)
                            ->then(function () use ($model) {
                                // Commit transaction if batch succeeds
                                DB::commit();
                                Log::info('Workflow batch completed successfully for model: ' . get_class($model));
                            })
                            ->catch(function ($batch, $e) {
                                // Rollback transaction if batch fails
                                DB::rollBack();
                                Log::error('Workflow batch failed. Rolled back transaction for model: ' . get_class($model), ['exception' => $e]);
                            })
                            ->dispatch();

                        return;
                    }
                }
            }

            // Commit transaction if no queueable jobs
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Workflow processing failed. Rolled back transaction for model: ' . get_class($model), ['exception' => $e]);
            throw $e;
        }
    }
}
