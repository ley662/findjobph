<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'icon', 'is_default'])]
class RequirementType extends Model
{
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function jobPosts(): BelongsToMany
    {
        return $this->belongsToMany(JobPost::class, 'job_post_requirements')
            ->withPivot('is_mandatory', 'custom_instruction')
            ->withTimestamps();
    }

    public function applicantDocuments(): HasMany
    {
        return $this->hasMany(ApplicantDocument::class);
    }
}
