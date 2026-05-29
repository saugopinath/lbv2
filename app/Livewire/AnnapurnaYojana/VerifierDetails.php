<?php

namespace App\Livewire\AnnapurnaYojana;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VerifierDetails extends Component
{
    public $familyId;
    public $family;
    public $members = [];
    public $verificationStatus;
    public $isApprover = false;

    public function mount($familyId, $isApprover = false): void
    {
        $this->familyId = (int)$familyId;
        $this->isApprover = (bool)$isApprover;
        $this->ensureSchema();
        $this->loadData();
    }

    /**
     * Run dynamic ALTER TABLE commands to ensure verification/approval remarks and document columns exist.
     */
    protected function ensureSchema(): void
    {
        try {
            $conn = DB::connection('pgsql_ay');

            // Columns to add to families
            $familiesCols = [
                'next_level_role_id' => 'SMALLINT DEFAULT 0',
                'is_reverted' => 'SMALLINT DEFAULT 0',
                'verification_datetime' => 'TIMESTAMP WITHOUT TIME ZONE',
                'approval_datetime' => 'TIMESTAMP WITHOUT TIME ZONE',
                'verification_remarks' => 'TEXT',
                'verification_doc_path' => 'TEXT',
                'approval_remarks' => 'TEXT',
                'approval_doc_path' => 'TEXT'
            ];

            foreach ($familiesCols as $colName => $colType) {
                $exists = $conn->selectOne("
                    SELECT 1 
                    FROM information_schema.columns 
                    WHERE table_schema = 'dbt_apy' 
                      AND table_name = 'families' 
                      AND column_name = ?
                ", [$colName]);

                if (!$exists) {
                    $conn->statement("ALTER TABLE dbt_apy.families ADD COLUMN {$colName} {$colType}");
                }
            }

            // Columns to add to family_members
            $membersCols = [
                'next_level_role_id' => 'SMALLINT DEFAULT 0',
                'is_reverted' => 'SMALLINT DEFAULT 0',
                'verification_datetime' => 'TIMESTAMP WITHOUT TIME ZONE',
                'approval_datetime' => 'TIMESTAMP WITHOUT TIME ZONE'
            ];

            foreach ($membersCols as $colName => $colType) {
                $exists = $conn->selectOne("
                    SELECT 1 
                    FROM information_schema.columns 
                    WHERE table_schema = 'dbt_apy' 
                      AND table_name = 'family_members' 
                      AND column_name = ?
                ", [$colName]);

                if (!$exists) {
                    $conn->statement("ALTER TABLE dbt_apy.family_members ADD COLUMN {$colName} {$colType}");
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Dynamic schema update failed: ' . $e->getMessage());
        }
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

        if ($this->family) {
            $f = $this->family;
            $f->district = !empty($f->lgd_district_code) ? \App\Models\District::find($f->lgd_district_code)?->name : null;
            if ($f->area_type === 'Rural') {
                $f->block = !empty($f->lgd_block_mc_code) ? \App\Models\Block::find($f->lgd_block_mc_code)?->name : null;
                $f->gp = !empty($f->lgd_gp_ward_code) ? \App\Models\Panchayat::find($f->lgd_gp_ward_code)?->name : null;
                $f->ward = null;
                $f->ulb = null;
            } else {
                $f->block = null;
                $f->gp = null;
                $f->ulb = !empty($f->ulb) ? \App\Models\Municipality::find($f->ulb)?->name : null;
                $f->ward = !empty($f->lgd_gp_ward_code) ? \App\Models\Ward::find($f->lgd_gp_ward_code)?->name : null;
            }
            $f->status = $f->application_status ?? null;
        }

        if (!$this->family) {
            session()->flash('error', 'Family application not found.');
            $this->redirect(route('annapurna-yojana-verification'));
            return;
        }

        // Dynamically compute verification status based on next_level_role_id
        $roleId = isset($this->family->next_level_role_id) ? (int)$this->family->next_level_role_id : 0;
        if ($roleId === 0) {
            $this->verificationStatus = 'Submitted';
        } elseif ($roleId === 50) {
            $this->verificationStatus = 'Verified';
        } elseif ($roleId === 100) {
            $this->verificationStatus = 'Approved';
        } elseif ($roleId === -50 || (isset($this->family->status) && strtolower($this->family->status) === 'reverted')) {
            $this->verificationStatus = 'Reverted';
        } elseif ($roleId < 0 || (isset($this->family->status) && strtolower($this->family->status) === 'rejected')) {
            $this->verificationStatus = 'Rejected';
        } else {
            $this->verificationStatus = $this->family->status ?? 'Submitted';
        }

        // ── 2. Fetch all members via raw SQL ────────────────────────────────
        $membersSql = "
            SELECT * 
            FROM   dbt_apy.family_members 
            WHERE  family_id = ? 
            ORDER  BY is_hof DESC, id ASC
        ";
        $this->members = DB::connection('pgsql_ay')->select($membersSql, [$this->familyId]);
        
        foreach ($this->members as $m) {
            $m->is_hof = $m->is_hof ?? false;
        }
    }


    public function render()
    {
        return view('livewire.annapurna-yojana.applicant-family-details');
    }
}
