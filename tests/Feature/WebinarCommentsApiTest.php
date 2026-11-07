<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class WebinarCommentsApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'webinar_testing');
        config()->set('database.connections.webinar_testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        DB::purge('webinar_testing');
        DB::setDefaultConnection('webinar_testing');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->timestamps();
        });
        Schema::create('webinars', function (Blueprint $table) {
            $table->id();
            $table->boolean('allow_qa')->default(true);
            $table->timestamps();
        });
        Schema::create('webinar_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('comment');
            $table->timestamps();
        });

        DB::table('users')->insert(['id' => 1, 'name' => 'Member', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('webinars')->insert(['id' => 1, 'allow_qa' => true, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('webinar_comments')->insert(['id' => 1, 'webinar_id' => 1, 'user_id' => 1, 'comment' => 'Hello', 'created_at' => now(), 'updated_at' => now()]);
    }

    public function test_long_poll_parameters_keep_existing_paginated_response_format(): void
    {
        $this->getJson('/api/webinars/1/comments?after_id=1&wait_seconds=0')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.current_page', 1)
            ->assertJsonPath('data.data.0.comment', 'Hello');
    }
}
