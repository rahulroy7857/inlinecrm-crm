<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StudentDocument extends Model
{
    protected $fillable = [
        'student_id',
        'document_type',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function typeLabel(): string
    {
        return config("student.document_types.{$this->document_type}", ucfirst(str_replace('_', ' ', $this->document_type)));
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function iconClass(): string
    {
        if ($this->isImage()) {
            return 'bx-image';
        }

        return 'bx-file';
    }

    public function formattedSize(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }

    public function deleteFile(): void
    {
        if ($this->file_path && Storage::disk('local')->exists($this->file_path)) {
            Storage::disk('local')->delete($this->file_path);
        }
    }
}
