<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

#[Fillable([
    'user_id',
    'category_id',
    'title',
    'company_name',
    'location',
    'job_type',
    'workplace_type',
    'salary_min',
    'salary_max',
    'salary_period',
    'description',
    'responsibilities',
    'qualifications',
    'benefits',
    'tags',
    'status',
    'views_count',
])]
class JobPost extends Model
{
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'salary_min' => 'integer',
            'salary_max' => 'integer',
            'views_count' => 'integer',
        ];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function requirements(): BelongsToMany
    {
        return $this->belongsToMany(RequirementType::class, 'job_post_requirements')
            ->withPivot('is_mandatory', 'custom_instruction')
            ->withTimestamps();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get missing mandatory requirements for a given applicant.
     *
     * @return Collection<int, RequirementType>
     */
    public function missingRequirementsFor(?User $applicant): Collection
    {
        $mandatoryReqs = $this->requirements->filter(fn ($req) => (bool) ($req->pivot->is_mandatory ?? true));

        if (! $applicant || ! $applicant->isApplicant()) {
            return $mandatoryReqs;
        }

        $applicantDocTypeIds = $applicant->documents->pluck('requirement_type_id')->all();

        return $mandatoryReqs->reject(fn ($req) => in_array($req->id, $applicantDocTypeIds, true));
    }

    /**
     * Check if applicant has uploaded all mandatory documents for this job.
     */
    public function hasApplicantSatisfiedRequirements(?User $applicant): bool
    {
        if (! $applicant || ! $applicant->isApplicant()) {
            return false;
        }

        return $this->missingRequirementsFor($applicant)->isEmpty();
    }

    /**
     * Check if applicant has already applied.
     */
    public function hasApplicantApplied(?User $applicant): bool
    {
        if (! $applicant) {
            return false;
        }

        return $this->applications->contains('applicant_id', $applicant->id);
    }

    /**
     * Format salary range nicely.
     */
    public function formattedSalary(): string
    {
        if ($this->salary_min && $this->salary_max) {
            return '₱'.number_format($this->salary_min).' - ₱'.number_format($this->salary_max).' / '.$this->salary_period;
        }

        if ($this->salary_min) {
            return 'From ₱'.number_format($this->salary_min).' / '.$this->salary_period;
        }

        if ($this->salary_max) {
            return 'Up to ₱'.number_format($this->salary_max).' / '.$this->salary_period;
        }

        return 'Competitive salary';
    }
}
