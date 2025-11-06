<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Coa;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * COA Policy
 * Handles authorization for Chart of Accounts operations
 * Following AI_DEVELOPMENT_GUIDELINES.md security principles
 */
class CoaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user can view any COA records
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view COA list
        return true;
    }

    /**
     * Determine if user can view specific COA record
     */
    public function view(User $user, Coa $coa): bool
    {
        // All authenticated users can view COA details
        return true;
    }

    /**
     * Determine if user can create COA records
     */
    public function create(User $user): bool
    {
        // Only admin or accountant can create COA
        // TODO: Implement proper role checking when User model has roles
        return in_array($user->email, [
            // Temporarily use email for role checking
            // Replace with proper role system: $user->hasRole(['admin', 'accountant'])
        ]) || $user->is_admin ?? false;
    }

    /**
     * Determine if user can update COA record
     */
    public function update(User $user, Coa $coa): bool
    {
        // Cannot update deleted/inactive accounts
        if (!$coa->isActive()) {
            return false;
        }

        // Only admin or accountant can update COA
        return $this->create($user);
    }

    /**
     * Determine if user can delete COA record
     */
    public function delete(User $user, Coa $coa): bool
    {
        // Only admin can delete COA
        return $user->is_admin ?? false;
    }

    /**
     * Determine if user can restore deleted COA record
     */
    public function restore(User $user, Coa $coa): bool
    {
        // Only admin can restore COA
        return $user->is_admin ?? false;
    }

    /**
     * Determine if user can permanently delete COA record
     */
    public function forceDelete(User $user, Coa $coa): bool
    {
        // Never allow permanent deletion for audit trail
        return false;
    }

    /**
     * Determine if user can manage COA hierarchy
     */
    public function manageHierarchy(User $user): bool
    {
        // Only admin can manage COA structure
        return $user->is_admin ?? false;
    }

    /**
     * Determine if user can export COA
     */
    public function export(User $user): bool
    {
        // Admin and accountant can export
        return $this->create($user);
    }

    /**
     * Determine if user can import COA
     */
    public function import(User $user): bool
    {
        // Only admin can import (high risk operation)
        return $user->is_admin ?? false;
    }
}
