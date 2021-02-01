<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;

class Address extends Model
{
    use SoftDeletes, LaravelVueDatatableTrait;

    protected $table = 'addresses';

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
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $dataTableColumns = [
        'address_line1' => [
            'searchable' => true,
        ],
        'address_line2' => [
            'searchable' => true,
        ],
        'address_line3' => [
            'searchable' => true,
        ],
        'town' => [
            'searchable' => true,
        ],
        'postcode' => [
            'searchable' => true,
        ],
        'county' => [
            'searchable' => true,
        ],
        'country_id' => [
            'searchable' => true,
        ],
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
