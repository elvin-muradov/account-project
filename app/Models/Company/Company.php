<?php

namespace App\Models\Company;

use App\Models\Employee;
use App\Models\Envelopes\Envelope;
use App\Models\User;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    protected $casts = [
        'company_emails' => 'array',
        'charter_files' => 'array',
        'tax_id_number_files' => 'array',
        'extract_files' => 'array',
        'creators_files' => 'array',
        'director_id_card_files' => 'array',
        'founding_decision_files' => 'array',
        'fixed_asset_files' => 'array',
        'director_id' => 'integer',
        'main_user_id' => 'integer',
        'accountant_id' => 'integer'
    ];

    public function mainUser(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'main_user_id');
    }

    public function director(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'director_id');
    }

    public function employees(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'company_id');
    }

    public function activityCodes(): HasMany
    {
        return $this->hasMany(ActivityCode::class, 'company_id');
    }

    public function fromCompanyEnvelopes(): HasMany
    {
        return $this->hasMany(Envelope::class, 'from_company_id');
    }

    public function toCompanyEnvelopes(): HasMany
    {
        return $this->hasMany(Envelope::class, 'to_company_id');
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class, 'company_id');
    }

    public function accountant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accountant_id');
    }

    public function scopeOrder($query, $order_by): void
    {
        if (is_array($order_by)) {
            foreach ($order_by as $order) {
                if (isset($order['related_table'])) {
                    $query->join($order['related_table'], $order['related_table'] . ' . id',
                        ' = ', self::getTable() . ' . ' . Str::singular($order['related_table']) . '_id')
                        ->select($order['related_table'] . ' . ' . $order['field'], self::getTable() . ' .*')
                        ->when(
                            $order['related_table'] && $order['field'] && $order['sort'],
                            function ($query) use ($order) {
                                $query->orderBy($order['field'], $order['sort']);
                            }
                        );
                } else {
                    $query
                        ->when(
                            $order['field'] && $order['sort'],
                            function ($query) use ($order) {
                                $query->orderBy($order['field'], $order['sort']);
                            }
                        );
                }
            }
        }
    }
}
