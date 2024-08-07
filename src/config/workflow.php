<?php

return [
    'models' => [], // { modelClass => workflowName }
    'workflows' => [], // { workflowName => [conditionClass1, conditionClass2, ...] }
    'retry' => [
        'attempts' => 3,
        'delay' => 30,
    ],
];
