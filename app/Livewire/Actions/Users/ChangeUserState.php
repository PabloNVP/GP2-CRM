<?php

namespace App\Livewire\Actions\Users;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Models\User;
use App\Support\AdminUserSecurityMessages;
use DomainException;

final readonly class ChangeUserState
{
    public function __invoke(int $userId, StateEnum $targetState, ?int $actorUserId = null): bool
    {
        $user = User::query()->findOrFail($userId);

        if ($user->state === $targetState) {
            throw new DomainException(AdminUserSecurityMessages::STATE_ALREADY_ASSIGNED);
        }

        if ($targetState === StateEnum::INACTIVE && $actorUserId !== null && $actorUserId === $userId) {
            throw new DomainException(AdminUserSecurityMessages::SELF_DEACTIVATION_FORBIDDEN);
        }

        if (
            $targetState === StateEnum::INACTIVE
            && $user->role === RoleEnum::ADMIN
            && $user->state === StateEnum::ACTIVE
        ) {
            $activeAdminsCount = User::query()
                ->where('role', RoleEnum::ADMIN->value)
                ->where('state', StateEnum::ACTIVE->value)
                ->count();

            if ($activeAdminsCount <= 1) {
                throw new DomainException(AdminUserSecurityMessages::LAST_ACTIVE_ADMIN_DEACTIVATION);
            }
        }

        return $user->update([
            'state' => $targetState->value,
        ]);
    }
}
