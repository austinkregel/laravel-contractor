<?php

namespace Kregel\Contractor\Models;

use Kregel\Contractor\Models\Contract;

trait Contractable
{
    public function contracts(){
        return $this->belongsToMany(Contract::class, 'contractor_related_models', 'contractor_id', 'related_model_id');
    }
}