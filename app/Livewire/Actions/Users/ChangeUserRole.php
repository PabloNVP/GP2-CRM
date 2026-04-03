<?php

namespace App\Livewire\Actions\Users;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Models\User;
use App\Support\AdminUserSecurityMessages;
use DomainException;

final readonly class ChangeUserRole
{
    public function __invoke(int $userId, RoleEnum $targetRole): bool
    {
        $user = User::query()->findOrFail($userId);

        if ($user->role === $targetRole) {
            throw new DomainException(AdminUserSecurityMessages::ROLE_ALREADY_ASSIGNED);
        }

        if (
            $user->role === RoleEnum::ADMIN
            && $targetRole !== RoleEnum::ADMIN
            && $user->state === StateEnum::ACTIVE
        ) {
            $activeAdminsCount = User::query()
                ->where('role', RoleEnum::ADMIN->value)
                ->where('state', StateEnum::ACTIVE->value)
                ->count();

            if ($activeAdminsCount <= 1) {
                throw new DomainException(AdminUserSecurityMessages::LAST_ACTIVE_ADMIN_ROLE_CHANGE);
            }
        }

        return $user->update([
            'role' => $targetRole->value,
        ]);
    }
}
