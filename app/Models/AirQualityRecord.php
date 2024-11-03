<?php

declare(strict_types=1);

namespace Mazur\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $city_id
 * @property float $pm10
 * @property float $pm2_5
 * @property float $nh3
 * @property float $o3
 * @property float $no
 * @property float $no2
 * @property float $so2
 * @property float $co
 * @property int $aqi
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property City $city
 */
final class AirQualityRecord extends Model
{
    protected $table = 'air_quality_records';

    protected $fillable = [
        'city_id',
        'pm10',
        'pm2_5',
        'nh3',
        'o3',
        'no',
        'no2',
        'so2',
        'co',
        'aqi',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
