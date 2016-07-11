<?php
namespace Kregel\Contractor\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kregel\FormModel\Traits\Formable;

class Paths extends Model
{
    use SoftDeletes, Formable;

    public $table = 'contractor_paths';

    protected $form_name = 'uuid';

    public $fillable = ['contract_id', 'uuid', 'path'];

    public $dates = ['deleted_at'];

    public function contract(){
        return $this->belongsTo(Contract::class);
    }
}
