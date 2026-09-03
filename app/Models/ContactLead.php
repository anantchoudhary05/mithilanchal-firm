<?php

namespace App\Models;

use Database\Factories\ContactLeadFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactLead extends Model
{
    /** @use HasFactory<ContactLeadFactory> */
    use HasFactory;

    public const STATUS_NEW = 'new';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_CLOSED = 'closed';

    /**
     * @var list<string>
     */
    public const REQUIREMENTS = [
        'Bulk Wholesale Makhana',
        'Premium Makhana',
        'Roasted Makhana',
        'Flavoured Makhana',
        'Private Label',
        'Export Enquiry',
        'Other',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'requirement',
        'quantity',
        'message',
        'status',
        'admin_notes',
        'ip_address',
    ];

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_CONTACTED => 'Contacted',
            self::STATUS_CLOSED => 'Closed',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? (string) $this->status;
    }

    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function markContacted(): void
    {
        $this->status = self::STATUS_CONTACTED;
        $this->save();
    }

    public function scopeIncoming(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_NEW);
    }
}
