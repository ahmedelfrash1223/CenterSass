<?php

namespace App\Services;

use App\Enums\MediaTypeEnum;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    protected string $disk = 'public';

    protected string $basePath = 'media';

    // =============================================
    // STORE
    // =============================================


    public function store(Model $model, UploadedFile|array $files, ?Model $user = null, int $sortStart = 0): array
    {
        if ($files instanceof UploadedFile) {
            $files = [$files];
        }

        $saved = [];
        $order = $sortStart;

        DB::transaction(function () use ($model, $files, $user, &$saved, &$order) {
            foreach ($files as $file) {
                if (!$file instanceof UploadedFile) {
                    continue;
                }

                $media = $this->saveFile($model, $file, $user, $order);
                $saved[] = $media;
                $order++;
            }
        });

        return $saved;
    }

    protected function saveFile(Model $model, UploadedFile $file, ?Model $user, int $sortOrder = 0): Media
    {
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $mimeType = $file->getMimeType() ?? 'application/octet-stream';
        $size = $file->getSize();
        $type = MediaTypeEnum::fromMime($mimeType);

        $storedName = Str::uuid() . '.' . $extension;
        $subPath = $this->resolveSubPath($model, $type);

        $path = $file->storeAs($subPath, $storedName, $this->disk);

        [$width, $height] = $this->extractDimensions($file, $type);

        return Media::create([
            'mediable_id'     => $model->getKey(),
            'mediable_type'   => $model->getMorphClass(),
            'file_name'       => $originalName,
            'stored_name'     => $storedName,
            'path'            => $path,
            'type'            => $type->value,
            'mime_type'       => $mimeType,
            'extension'       => $extension,
            'size'            => $size,
            'width'           => $width,
            'height'          => $height,
            'sort_order'      => $sortOrder,
            'created_by'      => $user?->getKey(),
            'created_by_type' => $user?->getMorphClass(),
        ]);
    }

    // =============================================
    // REPLACE
    // =============================================


    public function replace(Media $media, UploadedFile $newFile, ?Model $user = null): Media
    {
        return DB::transaction(function () use ($media, $newFile, $user) {
            $this->deleteFileFromDisk($media->path);

            $originalName = $newFile->getClientOriginalName();
            $extension = strtolower($newFile->getClientOriginalExtension() ?: $newFile->extension());
            $mimeType = $newFile->getMimeType() ?? 'application/octet-stream';
            $size = $newFile->getSize();
            $type = MediaTypeEnum::fromMime($mimeType);

            $storedName = Str::uuid() . '.' . $extension;
            $subPath = dirname($media->path);
            $path = $newFile->storeAs($subPath, $storedName, $this->disk);

            [$width, $height] = $this->extractDimensions($newFile, $type);

            $media->update([
                'file_name'       => $originalName,
                'stored_name'     => $storedName,
                'path'            => $path,
                'type'            => $type->value,
                'mime_type'       => $mimeType,
                'extension'       => $extension,
                'size'            => $size,
                'width'           => $width,
                'height'          => $height,
                'created_by'      => $user?->getKey() ?? $media->created_by,
                'created_by_type' => $user?->getMorphClass() ?? $media->created_by_type,
            ]);

            return $media->fresh();
        });
    }

    // =============================================
    // DELETE
    // =============================================

    public function delete(Media $media): bool
    {
        return DB::transaction(function () use ($media) {
            $this->deleteFileFromDisk($media->path);
            return (bool) $media->forceDelete();
        });
    }


    public function deleteAllFor(Model $model): int
    {
        $count = 0;

        DB::transaction(function () use ($model, &$count) {
            foreach ($model->media()->get() as $media) {
                $this->deleteFileFromDisk($media->path);
                $media->forceDelete();
                $count++;
            }
        });

        return $count;
    }

    // =============================================
    // HELPERS
    // =============================================

    protected function resolveSubPath(Model $model, MediaTypeEnum $type): string
    {
        $modelName = Str::plural(Str::snake(class_basename($model)));
        $year = now()->format('Y');
        $month = now()->format('m');

        return "{$this->basePath}/{$modelName}/{$type->value}/{$year}/{$month}";
    }

    protected function extractDimensions(UploadedFile $file, MediaTypeEnum $type): array
    {
        if ($type !== MediaTypeEnum::IMAGE) {
            return [null, null];
        }

        try {
            $dimensions = @getimagesize($file->getRealPath());

            if ($dimensions === false) {
                return [null, null];
            }

            return [(int) $dimensions[0], (int) $dimensions[1]];
        } catch (\Throwable $e) {
            return [null, null];
        }
    }

    protected function deleteFileFromDisk(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (Storage::disk($this->disk)->exists($path)) {
            Storage::disk($this->disk)->delete($path);
        }
    }

    public function disk(string $disk): static
    {
        $this->disk = $disk;
        return $this;
    }

    public function basePath(string $path): static
    {
        $this->basePath = trim($path, '/');
        return $this;
    }
}
