<?php

namespace AhmedEbead\WorkflowManager\Observers;

use Illuminate\Database\Eloquent\Model;
use AhmedEbead\WorkflowManager\Services\WorkflowService;

class WorkflowObserver
{
    protected $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    public function saved(Model $model): void
    {
        $this->workflowService->processModel($model);
    }
}
