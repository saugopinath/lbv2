<?php

namespace App\Livewire\AnnapurnaYojana;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VerifierDetails extends Component
{
    public $familyId;
    public $family;
    public $members = [];
    public $verificationStatus;
    public $actionRemarks = '';

    public function mount($familyId): void
    {
        $this->familyId = (int)$familyId;
        $this->loadData();
    }

    public function loadData(): void
    {
        // ── 1. Fetch family details via raw SQL ─────────────────────────────
        $familySql = "
            SELECT * 
            FROM   dbt_apy.families 
            WHERE  id = ? 
            LIMIT  1
        ";
        $this->family = DB::connection('pgsql_ay')->selectOne($familySql, [$this->familyId]);

        if (!$this->family) {
            session()->flash('error', 'Family application not found.');
            $this->redirect(route('annapurna-yojana-verification'));
            return;
        }

        $this->verificationStatus = $this->family->application_status ?? $this->family->status ?? 'Pending';

        // ── 2. Fetch all members via raw SQL ────────────────────────────────
        $membersSql = "
            SELECT * 
            FROM   dbt_apy.family_members 
            WHERE  family_id = ? 
            ORDER  BY is_ho_f DESC, id ASC
        ";
        $this->members = DB::connection('pgsql_ay')->select($membersSql, [$this->familyId]);
        
        foreach ($this->members as $m) {
            $m->is_hof = $m->is_ho_f ?? false;
        }
    }

    /**
     * Verify the family application
     */
    public function verifyApplication()
    {
        try {
            DB::connection('pgsql_ay')->update(
                "UPDATE dbt_apy.families SET application_status = 'Verified', updated_at = NOW() WHERE id = ?",
                [$this->familyId]
            );

            session()->flash('success', 'Family application verified successfully.');
            $this->loadData();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to verify application: ' . $e->getMessage());
        }
    }

    /**
     * Reject the family application
     */
    public function rejectApplication()
    {
        try {
            DB::connection('pgsql_ay')->update(
                "UPDATE dbt_apy.families SET application_status = 'Rejected', updated_at = NOW() WHERE id = ?",
                [$this->familyId]
            );

            session()->flash('success', 'Family application rejected.');
            $this->loadData();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to reject application: ' . $e->getMessage());
        }
    }

    /**
     * Revert the family application
     */
    public function revertApplication()
    {
        try {
            DB::connection('pgsql_ay')->update(
                "UPDATE dbt_apy.families SET application_status = 'Reverted', updated_at = NOW() WHERE id = ?",
                [$this->familyId]
            );

            session()->flash('success', 'Family application reverted back for correction.');
            $this->loadData();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to revert application: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.annapurna-yojana.verifier-details');
    }
}
