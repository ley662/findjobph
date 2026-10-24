<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'company_name', 'phone', 'headline', 'avatar', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEmployer(): bool
    {
        return $this->role === 'employer';
    }

    public function isApplicant(): bool
    {
        return $this->role === 'applicant';
    }

    public function jobPosts(): HasMany
    {
        return $this->hasMany(JobPost::class, 'user_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'applicant_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicantDocument::class, 'user_id');
    }

    public function hasDocument(int $requirementTypeId): bool
    {
        return $this->documents->contains('requirement_type_id', $requirementTypeId);
    }

    public function getDocument(int $requirementTypeId): ?ApplicantDocument
    {
        return $this->documents->firstWhere('requirement_type_id', $requirementTypeId);
    }
}
