<?php

namespace App\Models;

use App\Exigo\Exigo;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;

class Ambassador extends Model
{
    use SoftDeletes, LaravelVueDatatableTrait;

    /**
     * ID of the ambassador that represents the  company
     */
    const _AMBASSADOR_ID = '00000000-0000-0000-0000-000000000000';

    /**
     * @var array
     */
    protected $fillable = [
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
        'xxxxxxxxxxxx',
    ];

    protected $dataTableColumns = [
        'exigo_rank_id' => [
            'searchable' => true,
        ],
    ];

    public function getPicturePath()
    {
        return Storage::cloud()->url($this->picture_path);
    }

    /**
     * @return BelongsTo
     */
    public function legalStatus()
    {
        return $this->belongsTo(AmbassadorLegalStatus::class, 'legal_status_id', 'id');
    }

    public function homeAddress()
    {
        return $this->hasOne(Address::class, 'id', 'home_address');
    }

    public function deliveryAddress()
    {
        return $this->hasOne(Address::class, 'id', 'delivery_address');
    }
}
