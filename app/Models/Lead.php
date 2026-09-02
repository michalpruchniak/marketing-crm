<?php

namespace App\Models;

use App\Enums\LeadLabel;
use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $notes
 * @property LeadLabel $label
 * @property int|null $sales_id
 */
class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'notes',
        'label',
        'sales_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'label' => LeadLabel::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function salesPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_id');
    }
}
