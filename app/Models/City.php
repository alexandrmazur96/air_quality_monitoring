<?php

declare(strict_types=1);

namespace Mazur\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $country
 * @property string $state
 * @property string $latitude
 * @property string $longitude
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class City extends Model
{
    protected $table = 'cities';

    protected $fillable = [
        'name',
        'country',
        'state',
        'latitude',
        'longitude',
    ];
}
