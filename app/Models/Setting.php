<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'hide_from_search' => 'boolean',
        'show_cookie_banner' => 'boolean',
        'maintenance_mode' => 'boolean',
        'visible_sections' => 'array',
    ];

    protected $appends = ['logo_url'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo)) {
            return null;
        }

        if (str_starts_with($this->logo, 'http')) {
            return $this->logo;
        }

        if (config('filesystems.default') === 's3') {
            return \Illuminate\Support\Facades\Storage::disk('s3')->url($this->logo);
        }

        return asset('storage/' . $this->logo);
    }
}
