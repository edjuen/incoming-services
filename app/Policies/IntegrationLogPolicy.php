<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\IntegrationLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class IntegrationLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:IntegrationLog');
    }

    public function view(AuthUser $authUser, IntegrationLog $integrationLog): bool
    {
        return $authUser->can('View:IntegrationLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:IntegrationLog');
    }

    public function update(AuthUser $authUser, IntegrationLog $integrationLog): bool
    {
        return $authUser->can('Update:IntegrationLog');
    }

    public function delete(AuthUser $authUser, IntegrationLog $integrationLog): bool
    {
        return $authUser->can('Delete:IntegrationLog');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:IntegrationLog');
    }

    public function restore(AuthUser $authUser, IntegrationLog $integrationLog): bool
    {
        return $authUser->can('Restore:IntegrationLog');
    }

    public function forceDelete(AuthUser $authUser, IntegrationLog $integrationLog): bool
    {
        return $authUser->can('ForceDelete:IntegrationLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:IntegrationLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:IntegrationLog');
    }

    public function replicate(AuthUser $authUser, IntegrationLog $integrationLog): bool
    {
        return $authUser->can('Replicate:IntegrationLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:IntegrationLog');
    }

}