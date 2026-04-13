<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $fillable = [
        'name',
        'type',
        'channel',
        'template_id',
        'subject',
        'body',
        'file_path',
        'image_path',
        'use_qr',
        'qr_column',
        'name_column',
        'phone_column',
        'email_column',
        'status',
        'scheduled_at',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'use_qr'       => 'boolean',
        'scheduled_at' => 'datetime',
        'started_at'   => 'datetime',
        'finished_at'  => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(CampaignRecipient::class);
    }

    public function contactGroups(): BelongsToMany
    {
        return $this->belongsToMany(ContactGroup::class, 'campaign_contact_groups');
    }

    public function isTransactional(): bool
    {
        return $this->type === 'transactional';
    }

    public function isMarketing(): bool
    {
        return $this->type === 'marketing';
    }

    public function isWa(): bool
    {
        return $this->channel === 'wa';
    }

    public function isEmail(): bool
    {
        return $this->channel === 'email';
    }

    public function hasImage(): bool
    {
        return !empty($this->image_path);
    }

    public function getImageUrl(): ?string
    {
        return $this->image_path
            ? url('storage/' . $this->image_path)
            : null;
    }
}
