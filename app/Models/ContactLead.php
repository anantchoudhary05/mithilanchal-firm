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

    public function phoneDigits(): string
    {
        return preg_replace('/\D+/', '', (string) $this->phone) ?? '';
    }

    public function telHref(): ?string
    {
        $digits = $this->phoneDigits();

        if ($digits === '') {
            return null;
        }

        return 'tel:+91'.$this->internationalPhoneDigits($digits);
    }

    public function whatsappHref(): ?string
    {
        $digits = $this->phoneDigits();

        if ($digits === '') {
            return null;
        }

        $text = rawurlencode(
            'Hello '.($this->name ?: '').', regarding your '.($this->requirement ?: 'makhana').' enquiry with Mithilanchal Farms.'
        );

        return 'https://wa.me/91'.$this->internationalPhoneDigits($digits).'?text='.$text;
    }

    public function mailtoHref(): ?string
    {
        if (blank($this->email)) {
            return null;
        }

        $subject = rawurlencode('Your enquiry with Mithilanchal Farms');

        return 'mailto:'.$this->email.'?subject='.$subject;
    }

    public function receivedLabel(): string
    {
        $received = $this->created_at;

        if (! $received instanceof \DateTimeInterface) {
            return '—';
        }

        return $received->format('d M Y, h:i A');
    }

    /**
     * Strip a leading 91 country code so tel/WhatsApp links stay consistent.
     */
    private function internationalPhoneDigits(string $digits): string
    {
        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            return substr($digits, 2);
        }

        return $digits;
    }
}
