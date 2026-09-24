<?php

namespace App\Enums;

enum MediaTypeEnum: string
{
    case IMAGE  = 'image';
    case VIDEO  = 'video';
    case AUDIO  = 'audio';
    case PDF    = 'pdf';
    case DOC    = 'doc';
    case EXCEL  = 'excel';
    case ZIP    = 'zip';
    case FILE   = 'file';

    public function label(): string
    {
        return match ($this) {
            self::IMAGE => 'صورة',
            self::VIDEO => 'فيديو',
            self::AUDIO => 'صوت',
            self::PDF   => 'PDF',
            self::DOC   => 'مستند',
            self::EXCEL => 'Excel',
            self::ZIP   => 'ZIP',
            self::FILE  => 'ملف',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::IMAGE => 'ph-image',
            self::VIDEO => 'ph-video-camera',
            self::AUDIO => 'ph-music-notes',
            self::PDF   => 'ph-file-pdf',
            self::DOC   => 'ph-file-doc',
            self::EXCEL => 'ph-file-xls',
            self::ZIP   => 'ph-file-zip',
            self::FILE  => 'ph-file',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::IMAGE => 'primary',
            self::VIDEO => 'danger',
            self::AUDIO => 'info',
            self::PDF   => 'danger',
            self::DOC   => 'primary',
            self::EXCEL => 'success',
            self::ZIP   => 'warning',
            self::FILE  => 'secondary',
        };
    }

    public static function fromMime(string $mime): self
    {
        return match (true) {
            str_starts_with($mime, 'image/')   => self::IMAGE,
            str_starts_with($mime, 'video/')   => self::VIDEO,
            str_starts_with($mime, 'audio/')   => self::AUDIO,
            $mime === 'application/pdf'        => self::PDF,
            str_contains($mime, 'word')        => self::DOC,
            str_contains($mime, 'excel') ||
            str_contains($mime, 'spreadsheet') => self::EXCEL,
            str_contains($mime, 'zip') ||
            str_contains($mime, 'rar')         => self::ZIP,
            default                            => self::FILE,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($c) => [$c->value => $c->label()])
            ->toArray();
    }
}
