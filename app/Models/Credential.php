<?php

namespace App\Models;

use App\Supports\SecretsStorage\Enums\SecretsDriver;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property SecretsDriver $type
 */
class Credential extends Model
{
    use HasUlids;

    protected $fillable = [
        'client_id',
        'user_id',
        'uuid',
        'type',
        'name',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => SecretsDriver::class,
        ];
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): SecretsDriver
    {
        return $this->type;
    }
}
