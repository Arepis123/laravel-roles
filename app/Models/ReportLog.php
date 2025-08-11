<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_type',
        'report_format',
        'filters',
        'file_path',
        'file_name',
        'record_count',
        'generated_by',
        'generated_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'generated_at' => 'datetime',
    ];

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getFileSizeAttribute()
    {
        if ($this->file_path && file_exists(storage_path('app/' . $this->file_path))) {
            return number_format(filesize(storage_path('app/' . $this->file_path)) / 1024, 2) . ' KB';
        }
        return 'N/A';
    }
}