<?php

namespace App\Models;

use App\Enums\MediaTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use SoftDeletes;

    protected $table = 'media';

    protected $fillable = [
        'mediable_id',
        'mediable_type',
        'file_name',
        'stored_name',
        'path',
        'type',
        'mime_type',
        'extension',
        'size',
        'width',
        'height',
        'duration',
        'sort_order',
        'created_by',
        'created_by_type',
    ];

    protected $casts = [
        'size'       => 'integer',
        'width'      => 'integer',
        'height'     => 'integer',
        'duration'   => 'integer',
        'sort_order' => 'integer',
    ];

    // =============================================
    // Relations
    // =============================================

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): MorphTo
    {
        return $this->morphTo('creator', 'created_by_type', 'created_by');
    }

    // =============================================
    // Accessors
    // =============================================

    public function getTypeEnumAttribute(): MediaTypeEnum
    {
        return MediaTypeEnum::tryFrom($this->type) ?? MediaTypeEnum::FILE;
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type_enum->label();
    }

    public function getTypeIconAttribute(): string
    {
        return $this->type_enum->icon();
    }

    public function getTypeColorAttribute(): string
    {
        return $this->type_enum->color();
    }


    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }


    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;

        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        }

        if ($bytes < 1073741824) {
            return round($bytes / 1048576, 1) . ' MB';
        }

        return round($bytes / 1073741824, 2) . ' GB';
    }

    public function getIsImageAttribute(): bool
    {
        return $this->type === MediaTypeEnum::IMAGE->value;
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->type === MediaTypeEnum::VIDEO->value;
    }

    public function getIsPdfAttribute(): bool
    {
        return $this->type === MediaTypeEnum::PDF->value;
    }
}
