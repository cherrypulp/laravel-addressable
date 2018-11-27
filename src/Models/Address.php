<?php

/*
 * This file is part of Laravel Addressable.
 *
 * (c) Brian Faust <hello@brianfaust.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blok\Addressable\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class Address extends Model
{
    /** @var array */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(config('laravel-addressable.models.country'));
    }

    /**
     * Change the role of the current address model.
     *
     * @param string $role
     *
     * @return bool
     */
    public function role($role)
    {
        return $this->update(compact('role'));
    }


    /**
     *
     * @param Builder $query
     * @param $lat
     * @param $lng
     * @return Builder
     * @internal param $place
     * @internal param $distance
     */
    public function scopeCloseTo($query, $lat, $lng)
    {
        return $query->select(DB::raw("*,
                          ( 3959 * acos( cos( radians(?) ) *
                            cos( radians( lat ) )
                            * cos( radians( lng ) - radians(?)
                            ) + sin( radians(?) ) *
                            sin( radians( lat ) ) )
                          ) AS distance"))
            ->setBindings([$lat, $lng, $lat], 'select');
    }
}
