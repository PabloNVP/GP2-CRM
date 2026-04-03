<?php

namespace App\Livewire\Admin;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Livewire\Actions\Users\ChangeUserRole;
use App\Livewire\Actions\Users\ChangeUserState;
use App\Livewire\Actions\Users\ListingUsers;
use App\Models\User;
use App\Support\AdminUserSecurityMessages;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;
use Livewire\WithPagination;

final class IndexUsers extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public string $stateFilter = '';
    public bool $isRoleModalVisible = false;
    public ?int $userIdForRoleAction = null;
    public string $pendingRoleValue = '';
    public string $roleModalTitle = 'Confirmar cambio de rol';
    public string $roleModalMessage = 'Desea continuar con esta accion?';
    public bool $isStateModalVisible = false;
    public ?int $userIdForStateAction = null;
    public string $pendingStateValue = '';
    public string $stateModalTitle = 'Confirmar cambio de estado';
    public string $stateModalMessage = 'Desea continuar con esta accion?';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStateFilter(): void
    {
        $this->resetPage();
    }

    public function openRoleModal(int $userId, string $nextRole): void
    {
        $targetRole = RoleEnum::tryFrom($nextRole);

        if (! $targetRole) {
            session()->flash('error', AdminUserSecurityMessages::INVALID_ROLE);

            return;
        }

        $user = User::query()->find($userId, ['id', 'name', 'role']);

        if (! $user) {
            session()->flash('error', AdminUserSecurityMessages::USER_NOT_FOUND);

            return;
        }

        if ($user->role === $targetRole) {
            session()->flash('error', AdminUserSecurityMessages::ROLE_ALREADY_ASSIGNED);

            return;
        }

        $this->userIdForRoleAction = $userId;
        $this->pendingRoleValue = $targetRole->value;
        $this->isRoleModalVisible = true;
        $this->roleModalTitle = 'Confirmar cambio de rol';
        $this->roleModalMessage = "Se cambiara el rol de {$user->name} a {$targetRole->value}. Desea continuar?";
    }

    public function cancelRoleChange(): void
    {
        $this->resetRoleModal();
    }

    public function confirmRoleChange(ChangeUserRole $changeUserRole): void
    {
        if (! $this->userIdForRoleAction || $this->pendingRoleValue === '') {
            session()->flash('error', AdminUserSecurityMessages::ROLE_PENDING_ACTION_MISSING);
            $this->resetRoleModal();

            return;
        }

        $targetRole = RoleEnum::tryFrom($this->pendingRoleValue);

        if (! $targetRole) {
            session()->flash('error', AdminUserSecurityMessages::INVALID_ROLE);
            $this->resetRoleModal();

            return;
        }

        try {
            $changeUserRole($this->userIdForRoleAction, $targetRole);
            session()->flash('message', AdminUserSecurityMessages::ROLE_UPDATED_SUCCESS);
        } catch (ModelNotFoundException) {
            session()->flash('error', AdminUserSecurityMessages::USER_NOT_FOUND);
        } catch (DomainException $exception) {
            session()->flash('error', $exception->getMessage());
        }

        $this->resetRoleModal();
    }

    public function openStateModal(int $userId, string $nextState): void
    {
        $targetState = StateEnum::tryFrom($nextState);

        if (! $targetState) {
            session()->flash('error', AdminUserSecurityMessages::INVALID_STATE);

            return;
        }

        $user = User::query()->find($userId, ['id', 'name', 'state']);

        if (! $user) {
            session()->flash('error', AdminUserSecurityMessages::USER_NOT_FOUND);

            return;
        }

        if ($user->state === $targetState) {
            session()->flash('error', AdminUserSecurityMessages::STATE_ALREADY_ASSIGNED);

            return;
        }

        if ($targetState === StateEnum::INACTIVE && auth()->id() === $user->id) {
            session()->flash('error', AdminUserSecurityMessages::SELF_DEACTIVATION_FORBIDDEN);

            return;
        }

        $this->userIdForStateAction = $userId;
        $this->pendingStateValue = $targetState->value;
        $this->isStateModalVisible = true;
        $this->stateModalTitle = 'Confirmar cambio de estado';
        $this->stateModalMessage = "Se cambiara el estado de {$user->name} a {$targetState->value}. Desea continuar?";
    }

    public function cancelStateChange(): void
    {
        $this->resetStateModal();
    }

    public function confirmStateChange(ChangeUserState $changeUserState): void
    {
        if (! $this->userIdForStateAction || $this->pendingStateValue === '') {
            session()->flash('error', AdminUserSecurityMessages::STATE_PENDING_ACTION_MISSING);
            $this->resetStateModal();

            return;
        }

        $targetState = StateEnum::tryFrom($this->pendingStateValue);

        if (! $targetState) {
            session()->flash('error', AdminUserSecurityMessages::INVALID_STATE);
            $this->resetStateModal();

            return;
        }

        try {
            $changeUserState($this->userIdForStateAction, $targetState, auth()->id());
            session()->flash('message', AdminUserSecurityMessages::STATE_UPDATED_SUCCESS);
        } catch (ModelNotFoundException) {
            session()->flash('error', AdminUserSecurityMessages::USER_NOT_FOUND);
        } catch (DomainException $exception) {
            session()->flash('error', $exception->getMessage());
        }

        $this->resetStateModal();
    }

    public function render(ListingUsers $listingUsers): View
    {
        return view('admin.index-users', [
            'users' => $listingUsers(
                search: $this->search,
                roleFilter: $this->roleFilter,
                stateFilter: $this->stateFilter,
            ),
            'roles' => RoleEnum::cases(),
            'states' => StateEnum::cases(),
        ]);
    }

    private function resetRoleModal(): void
    {
        $this->isRoleModalVisible = false;
        $this->userIdForRoleAction = null;
        $this->pendingRoleValue = '';
        $this->roleModalTitle = 'Confirmar cambio de rol';
        $this->roleModalMessage = 'Desea continuar con esta accion?';
    }

    private function resetStateModal(): void
    {
        $this->isStateModalVisible = false;
        $this->userIdForStateAction = null;
        $this->pendingStateValue = '';
        $this->stateModalTitle = 'Confirmar cambio de estado';
        $this->stateModalMessage = 'Desea continuar con esta accion?';
    }
}
