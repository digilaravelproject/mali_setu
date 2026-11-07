<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\LocationManagementController;
use App\Models\District;
use App\Models\Locality;
use App\Models\Taluka;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminLocationFiltersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'location_testing');
        config()->set('database.connections.location_testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        DB::purge('location_testing');
        DB::setDefaultConnection('location_testing');

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('talukas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('localities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taluka_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $pune = District::create(['name' => 'Pune', 'is_active' => true]);
        $satara = District::create(['name' => 'Satara', 'is_active' => false]);
        $haveli = Taluka::create(['district_id' => $pune->id, 'name' => 'Haveli', 'is_active' => true]);
        $karad = Taluka::create(['district_id' => $satara->id, 'name' => 'Karad', 'is_active' => false]);
        Locality::create(['taluka_id' => $haveli->id, 'name' => 'Wagholi', 'type' => 'village', 'is_active' => true]);
        Locality::create(['taluka_id' => $karad->id, 'name' => 'Karad City', 'type' => 'city', 'is_active' => false]);
    }

    public function test_district_taluka_and_locality_filters_are_applied(): void
    {
        $controller = app(LocationManagementController::class);

        $districts = $controller->districts(Request::create('/', 'GET', ['status' => 'inactive']))->getData()['districts'];
        $this->assertSame(['Satara'], $districts->pluck('name')->all());

        $puneId = District::where('name', 'Pune')->value('id');
        $talukas = $controller->talukas(Request::create('/', 'GET', ['district_id' => $puneId, 'search' => 'Have']))->getData()['talukas'];
        $this->assertSame(['Haveli'], $talukas->pluck('name')->all());

        $localities = $controller->localities(Request::create('/', 'GET', ['district_id' => $puneId, 'type' => 'village', 'status' => 'active']))->getData()['localities'];
        $this->assertSame(['Wagholi'], $localities->pluck('name')->all());
    }
}
