<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Database\Factories\JobApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    /** @use HasFactory<\Database\Factories\JobApplicationFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'role_title',
        'status',
        'salary_min',
        'salary_max',
        'applied_on',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array //tukar bahasa php kepada bahasa database and vice versa.
    {
        return [
            'status' => ApplicationStatus::class, // 'nama_kolum_database' => NamaFailEnum::class
            'applied_on' => 'date',
        ];
    }

    public function company(): BelongsTo //1 job application belongs to 1 company.
    {
        return $this->belongsTo(Company::class); //supaya boleh access data between 2 tables.
    }
}
