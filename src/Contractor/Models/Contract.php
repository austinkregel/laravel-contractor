<?php

namespace Kregel\Contractor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kregel\FormModel\Traits\Formable;

class Contract extends Model
{
    use SoftDeletes, Formable;

    protected $form_name = 'name';

    protected $table = 'contractor_contracts';

    protected $hidden = [];

    protected $fillable = [
        'name', 'description', 'who_its_through', 'started_at', 'ended_at', 'path', 'uuid', 'old_contract', 'user_id'
    ];

    protected $dates = [
        'ended_at',
        'started_at',
        'deleted_at'
    ];
    public function contractors()
    {
        return $this->belongsToMany(config('kregel.contractor.related_model'), 'contractor_related_models', 'contractor_id', 'related_model_id');
    }

    public function paths()
    {
        return $this->hasMany(Paths::class, 'contract_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(config('kregel.contractor.user_model'));
    }

    public function old()
    {
        return $this->belongsTo(Contract::class, 'old_contract');
    }

    /*
     So how can we get the notifications to the right people? I guess in a config file we can get the user_id
     */

    public function notify()
    {
        $related_key = config('kregel.contractor.related_model_key');

        $notify = [];

        foreach ($this->contractors as $contractor) {
            $notify[] = $contractor->$related_key;
        }
        return collect($notify);
    }
}
