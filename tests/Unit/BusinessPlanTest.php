<?php

namespace Tests\Unit;

use App\Models\BusinessPlan;
use PHPUnit\Framework\TestCase;

class BusinessPlanTest extends TestCase
{
    public function test_legacy_company_type_labels_match_new_owner_label(): void
    {
        $legacy = BusinessPlan::normalizeCompanyType('Proprietary /Partnership - LLP');
        $current = BusinessPlan::normalizeCompanyType('Proprietary (Owner)/Partnership - LLP');

        $this->assertSame($legacy, $current);
    }
}
