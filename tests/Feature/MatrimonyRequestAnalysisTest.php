<?php

namespace Tests\Feature;

use App\Models\ConnectionRequest;
use App\Models\MatrimonyProfile;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MatrimonyRequestAnalysisTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('database.default', 'request_analysis_testing');
        config()->set('database.connections.request_analysis_testing', [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]);
        DB::purge('request_analysis_testing');
        DB::setDefaultConnection('request_analysis_testing');

        Schema::create('users', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('email')->nullable(); $table->string('phone')->nullable();
            $table->string('password')->nullable(); $table->string('photo')->nullable(); $table->string('user_type')->default('general');
            $table->string('cast_certificate')->nullable(); $table->timestamps();
        });
        Schema::create('caste_certificates', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id'); $table->string('file_path')->nullable(); $table->timestamps();
        });
        Schema::create('matrimony_profiles', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id'); $table->string('gender')->nullable(); $table->integer('age')->nullable();
            $table->json('personal_details')->nullable(); $table->json('location_details')->nullable(); $table->timestamps();
        });
        Schema::create('connection_requests', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('sender_id'); $table->unsignedBigInteger('receiver_id');
            $table->string('status')->default('pending'); $table->text('message')->nullable(); $table->text('response_message')->nullable();
            $table->timestamp('responded_at')->nullable(); $table->timestamps();
        });

        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'user_type' => 'admin']);
        $sender = User::create(['name' => 'Account Name', 'email' => 'kapil@example.test', 'phone' => '+91 98813-91035']);
        $receiver = User::create(['name' => 'Laxmi Jaikumar', 'email' => 'laxmi@example.test', 'phone' => '+918369854727']);
        MatrimonyProfile::create(['user_id' => $sender->id, 'age' => 31, 'personal_details' => ['first_name' => 'Kapil', 'last_name' => 'Holey', 'gender' => 'male'], 'location_details' => ['city' => 'Pune']]);
        MatrimonyProfile::create(['user_id' => $receiver->id, 'age' => 29, 'personal_details' => ['first_name' => 'Laxmi', 'last_name' => 'Charjan', 'gender' => 'female'], 'location_details' => ['city' => 'Mumbai']]);
        ConnectionRequest::create(['sender_id' => $sender->id, 'receiver_id' => $receiver->id, 'status' => 'pending']);
        $this->actingAs($admin);
    }

    public function test_search_finds_account_name_candidate_name_and_formatted_mobile(): void
    {
        foreach (['Account Name', 'Kapil Holey', '9881391035', 'Laxmi Jaikumar'] as $search) {
            $this->get(route('admin.matrimony.connections', ['search' => $search]))
                ->assertOk()->assertSee('Kapil Holey')->assertDontSee('No matrimony requests match these filters.');
        }
    }

    public function test_request_analysis_renders_reference_layout_controls(): void
    {
        $this->get(route('admin.matrimony.connections'))->assertOk()
            ->assertSee('Request Details')->assertSee('From Member')->assertSee('To Member')
            ->assertSee('Age Range')->assertSee('Sent Requests')->assertSee('Received Requests');
    }
}
