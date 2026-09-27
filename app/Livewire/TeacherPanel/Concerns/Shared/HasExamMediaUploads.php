<?php

namespace App\Livewire\TeacherPanel\Concerns\Shared;

use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamQuestionOption;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait HasExamMediaUploads
{
    /**
     * حالة الرفع — كل مفتاح بيشاور على السؤال/الاختيار اللي بيترفع حالياً.
     * مثال: "question.0" => true  /  "option.0.1" => true
     */
    public array $uploading = [];

    public function isUploading(string $key): bool
    {
        return !empty($this->uploading[$key]);
    }

    public function hasAnyUploadInProgress(): bool
    {
        return collect($this->uploading)->contains(fn ($v) => (bool) $v);
    }

    // =============================================
    // Upload Question Image
    // =============================================

    public function uploadQuestionImage(int $questionIndex, $file): void
    {
        $key = "question.{$questionIndex}";
        $this->uploading[$key] = true;

        try {
            $uploaded = $this->normalizeUpload($file);

            if (!$uploaded) {
                $this->dispatch('show-error-message', message: 'لم يتم استلام الصورة.');
                return;
            }

            $user = Auth::guard('teacher')->user();

            // نحتاج موديل مؤقت عشان الـ morph
            $temp = new ExamQuestion();
            $temp->exam_id = 0;
            $temp->exists = false;

            /** @var MediaService $service */
            $service = app(MediaService::class);

            $media = $service->store($temp, $uploaded, $user)[0] ?? null;

            if (!$media) {
                $this->dispatch('show-error-message', message: 'فشل رفع الصورة.');
                return;
            }

            // نخزّن الـ id مؤقتاً في السؤال
            $this->questions[$questionIndex]['media_id']  = $media->id;
            $this->questions[$questionIndex]['media_url'] = $media->url;

            $this->dispatch('show-success-message', message: 'تم رفع صورة السؤال.');
        } finally {
            unset($this->uploading[$key]);
        }
    }

    public function removeQuestionImage(int $questionIndex): void
    {
        $mediaId = $this->questions[$questionIndex]['media_id'] ?? null;

        if ($mediaId) {
            $media = Media::find($mediaId);

            if ($media) {
                app(MediaService::class)->delete($media);
            }
        }

        $this->questions[$questionIndex]['media_id']  = null;
        $this->questions[$questionIndex]['media_url'] = null;
    }

    // =============================================
    // Upload Option Image
    // =============================================

    public function uploadOptionImage(int $questionIndex, int $optionIndex, $file): void
    {
        $key = "option.{$questionIndex}.{$optionIndex}";
        $this->uploading[$key] = true;

        try {
            $uploaded = $this->normalizeUpload($file);

            if (!$uploaded) {
                $this->dispatch('show-error-message', message: 'لم يتم استلام الصورة.');
                return;
            }

            $user = Auth::guard('teacher')->user();

            $temp = new ExamQuestionOption();
            $temp->exam_question_id = 0;
            $temp->exists = false;

            $media = app(MediaService::class)->store($temp, $uploaded, $user)[0] ?? null;

            if (!$media) {
                $this->dispatch('show-error-message', message: 'فشل رفع الصورة.');
                return;
            }

            $this->questions[$questionIndex]['options'][$optionIndex]['media_id']  = $media->id;
            $this->questions[$questionIndex]['options'][$optionIndex]['media_url'] = $media->url;

            $this->dispatch('show-success-message', message: 'تم رفع صورة الاختيار.');
        } finally {
            unset($this->uploading[$key]);
        }
    }

    public function removeOptionImage(int $questionIndex, int $optionIndex): void
    {
        $mediaId = $this->questions[$questionIndex]['options'][$optionIndex]['media_id'] ?? null;

        if ($mediaId) {
            $media = Media::find($mediaId);

            if ($media) {
                app(MediaService::class)->delete($media);
            }
        }

        $this->questions[$questionIndex]['options'][$optionIndex]['media_id']  = null;
        $this->questions[$questionIndex]['options'][$optionIndex]['media_url'] = null;
    }

    // =============================================
    // Helpers
    // =============================================

    /**
     * Livewire ممكن يبعت TemporaryUploadedFile أو UploadedFile أو string (path).
     */
    protected function normalizeUpload($file): ?UploadedFile
    {
        if ($file instanceof TemporaryUploadedFile) {
            return $file;
        }

        if ($file instanceof UploadedFile) {
            return $file;
        }

        if (is_string($file) && file_exists($file)) {
            return new UploadedFile($file, basename($file), null, null, true);
        }

        return null;
    }
}
