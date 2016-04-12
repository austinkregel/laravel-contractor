<?php

namespace Kregel\Contractor\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{

    protected $table = 'contractor_contracts';

    protected $hidden = [];

    protected $fillable = [
        'name', 'description', 'uuid', 'notification_date'
    ];

    protected $dates = [
        'notification_date'
    ];

    public function contractors(){
        return $this->belongsToMany(config('kregel.contractor.related_model'), 'contractor_related_models','contractor_id', 'related_model_id');
    }

    public function old(){
        return $this->belongsTo(Contract::class, 'old_contract');
    }

    public function notify(){
        $related_key = config('kregel.contractor.related_model_key');

        $notify = [];

        foreach($this->contractors as $contractor){
            $notify[] = $contractor->$related_key;
        }
        dd(collect($notify)->unique(), $this->contractors);
        return collect($notify);
    }
    /*
     So how can we get the notifications to the right people? I guess in a config file we can get the user_id
     */
}
