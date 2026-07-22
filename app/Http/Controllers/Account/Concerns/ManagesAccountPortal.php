<?php

namespace App\Http\Controllers\Account\Concerns;

trait ManagesAccountPortal
{
    protected function accountCanManage(): bool
    {
        return account_can_manage();
    }

    protected function accountActor()
    {
        return account_actor();
    }

    protected function accountCreatedById(): ?int
    {
        return is_admin_account_portal() ? null : auth()->guard('account')->id();
    }

    protected function authorizeAccountManage(): void
    {
        if (!$this->accountCanManage()) {
            abort(403, 'You do not have permission to perform this action.');
        }
    }

    protected function authorizeAccountAdminEdit(): void
    {
        if (! is_admin_account_portal() || ! $this->accountCanManage()) {
            abort(403, 'Only admins can edit transactions.');
        }
    }
}
