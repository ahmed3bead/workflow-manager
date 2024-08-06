<?php

namespace AhmedEbead\WorkflowManager\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $fillable = ['name', 'condition_id', 'action_class'];
}
