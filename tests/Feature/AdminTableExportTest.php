<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminTableExportTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_export_a_listing_to_excel_and_pdf(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin']);
        $payload = [
            'title' => 'Volunteer Listing',
            'headings' => ['Name', 'Commission'],
            'rows' => [
                ['Asha Patil', '250'],
                ['Ravi Mali', '1250'],
            ],
        ];

        $this->actingAs($admin)
            ->postJson(route('admin.table-export.excel'), $payload)
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->actingAs($admin)
            ->postJson(route('admin.table-export.pdf'), $payload)
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
