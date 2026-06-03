<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\IntegrationProvider;
use Illuminate\Auth\Access\HandlesAuthorization;

class IntegrationProviderPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:IntegrationProvider');
    }

    public function view(AuthUser $authUser, IntegrationProvider $integrationProvider): bool
    {
        return $authUser->can('View:IntegrationProvider');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:IntegrationProvider');
    }

    public function update(AuthUser $authUser, IntegrationProvider $integrationProvider): bool
    {
        return $authUser->can('Update:IntegrationProvider');
    }

    public function delete(AuthUser $authUser, IntegrationProvider $integrationProvider): bool
    {
        return $authUser->can('Delete:IntegrationProvider');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:IntegrationProvider');
    }

    public function restore(AuthUser $authUser, IntegrationProvider $integrationProvider): bool
    {
        return $authUser->can('Restore:IntegrationProvider');
    }

    public function forceDelete(AuthUser $authUser, IntegrationProvider $integrationProvider): bool
    {
        return $authUser->can('ForceDelete:IntegrationProvider');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:IntegrationProvider');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:IntegrationProvider');
    }

    public function replicate(AuthUser $authUser, IntegrationProvider $integrationProvider): bool
    {
        return $authUser->can('Replicate:IntegrationProvider');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:IntegrationProvider');
    }

}