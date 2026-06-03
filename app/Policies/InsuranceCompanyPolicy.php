<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\InsuranceCompany;
use Illuminate\Auth\Access\HandlesAuthorization;

class InsuranceCompanyPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:InsuranceCompany');
    }

    public function view(AuthUser $authUser, InsuranceCompany $insuranceCompany): bool
    {
        return $authUser->can('View:InsuranceCompany');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:InsuranceCompany');
    }

    public function update(AuthUser $authUser, InsuranceCompany $insuranceCompany): bool
    {
        return $authUser->can('Update:InsuranceCompany');
    }

    public function delete(AuthUser $authUser, InsuranceCompany $insuranceCompany): bool
    {
        return $authUser->can('Delete:InsuranceCompany');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:InsuranceCompany');
    }

    public function restore(AuthUser $authUser, InsuranceCompany $insuranceCompany): bool
    {
        return $authUser->can('Restore:InsuranceCompany');
    }

    public function forceDelete(AuthUser $authUser, InsuranceCompany $insuranceCompany): bool
    {
        return $authUser->can('ForceDelete:InsuranceCompany');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:InsuranceCompany');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:InsuranceCompany');
    }

    public function replicate(AuthUser $authUser, InsuranceCompany $insuranceCompany): bool
    {
        return $authUser->can('Replicate:InsuranceCompany');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:InsuranceCompany');
    }

}