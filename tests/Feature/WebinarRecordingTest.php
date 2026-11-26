<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\WebinarManagementController;
use App\Models\Webinar;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class WebinarRecordingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('database.default', 'webinar_recording_testing');
        config()->set('database.connections.webinar_recording_testing', [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]);
        DB::purge('webinar_recording_testing');
        DB::setDefaultConnection('webinar_recording_testing');

        Schema::create('webinars', function (Blueprint $table) {
            $table->id();
            $table->boolean('allow_recording')->default(false);
            $table->string('recording_path')->nullable();
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();
        });
        Storage::fake('public');
    }

    public function test_recording_chunks_are_combined_and_saved_on_the_public_disk(): void
    {
        $webinar = Webinar::forceCreate(['allow_recording' => true]);
        $recordingId = (string) Str::uuid();
        $controller = app(WebinarManagementController::class);

        $first = Request::create('/', 'POST', [
            'recording_id' => $recordingId,
            'chunk_index' => 0,
            'is_final' => false,
        ], [], ['chunk' => UploadedFile::fake()->createWithContent('chunk-0.webm', 'first')]);
        $this->assertSame(200, $controller->uploadRecordingChunk($first, $webinar)->getStatusCode());

        $last = Request::create('/', 'POST', [
            'recording_id' => $recordingId,
            'chunk_index' => 1,
            'is_final' => true,
        ], [], ['chunk' => UploadedFile::fake()->createWithContent('chunk-1.webm', 'second')]);
        $this->assertSame(200, $controller->uploadRecordingChunk($last, $webinar)->getStatusCode());

        $webinar->refresh();
        $this->assertNotNull($webinar->recorded_at);
        $this->assertStringStartsWith('webinars/recordings/webinar-'.$webinar->id.'-', $webinar->recording_path);
        Storage::disk('public')->assertExists($webinar->recording_path);
        $this->assertSame('firstsecond', Storage::disk('public')->get($webinar->recording_path));
    }
}
