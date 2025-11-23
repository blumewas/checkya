<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\ApiSuite;
use Chiiya\FilamentAccessControl\Models\FilamentUser;

class ApiSuitePolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(FilamentUser $filamentUser, string $ability): ?bool
    {
        if ($filamentUser->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(FilamentUser $filamentUser): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(FilamentUser $filamentUser, ApiSuite $apiSuite): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(FilamentUser $filamentUser): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(FilamentUser $filamentUser, ApiSuite $apiSuite): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(FilamentUser $filamentUser, ApiSuite $apiSuite): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(FilamentUser $filamentUser, ApiSuite $apiSuite): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(FilamentUser $filamentUser, ApiSuite $apiSuite): bool
    {
        return false;
    }
}
