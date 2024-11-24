<?php

declare(strict_types=1);

namespace Mazur\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $chat_id
 * @property float $first_name
 * @property float $last_name
 * @property City $nearestCity
 */
final class UserLocation extends Model
{
    protected $table = 'users_location';
    protected $fillable = ['chat_id', 'latitude', 'longitude', 'location', 'nearest_city_id'];

    public function nearestCity(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
