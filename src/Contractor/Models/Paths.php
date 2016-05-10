<?php
/**
 * Created by PhpStorm.
 * User: sodium-chloride
 * Date: 4/20/2016
 * Time: 5:05 PM
 */

namespace Kregel\Contractor\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Paths extends Model
{
    use SoftDeletes;
    public $table = 'contractor_paths';

    public $fillable = ['contract_id', 'uuid', 'path'];

    public $dates = ['deleted_at'];

    public function contract(){
        return $this->belongsTo(Contract::class);
    }
}