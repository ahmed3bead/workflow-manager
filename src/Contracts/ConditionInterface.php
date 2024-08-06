<?php

namespace AhmedEbead\WorkflowManager\Contracts;

interface ConditionInterface
{
    public function check($model);
}
