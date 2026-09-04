<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminBusinessAndReportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'admin_testing');
        config()->set('database.connections.admin_testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        DB::purge('admin_testing');
        DB::setDefaultConnection('admin_testing');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('user_type')->default('general');
            $table->string('status')->default('active');
            $table->timestamps();
        });
        Schema::create('business_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('caste_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
        });
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');
            foreach (['business_name', 'business_type', 'description', 'country', 'state', 'city', 'pincode', 'verification_status', 'status'] as $column) {
                $table->string($column);
            }
            $table->string('photo')->nullable();
            $table->string('subscription_status')->default('trial');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->timestamps();
        });
        Schema::create('matrimony_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('gender')->nullable();
            $table->json('personal_details')->nullable();
            $table->string('approval_status')->default('pending');
            $table->timestamp('profile_expires_at')->nullable();
            $table->timestamps();
        });
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('transaction_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        DB::table('users')->insert([
            'id' => 1, 'name' => 'Test Admin', 'email' => 'admin@example.test',
            'user_type' => 'admin', 'created_at' => '2026-08-27 10:00:00',
        ]);
        DB::table('business_categories')->insert(['id' => 1, 'name' => 'Services']);
        $this->actingAs(User::findOrFail(1));
        Storage::fake('public');
    }

    private function businessData(): array
    {
        return [
            'user_id' => 1, 'business_name' => 'Photo Test Business',
            'business_type' => 'Private Ltd', 'category_id' => 1,
            'description' => 'Business photo regression test', 'country' => 'India',
            'state' => 'Maharashtra', 'city' => 'Pune', 'pincode' => '411001',
            'verification_status' => 'pending', 'status' => 'active',
        ];
    }

    public function test_business_can_be_created_with_a_photo(): void
    {
        $response = $this->post(route('admin.businesses.store'), [
            ...$this->businessData(), 'photo' => UploadedFile::fake()->image('business.jpg'),
        ]);

        $response->assertSessionHasNoErrors();
        $business = Business::firstOrFail();
        $response->assertRedirect(route('admin.businesses.show', $business));
        Storage::disk('public')->assertExists($business->photo);
    }

    public function test_business_photo_can_be_replaced(): void
    {
        Storage::disk('public')->put('business/photos/old.jpg', 'old photo');
        $business = Business::create([...$this->businessData(), 'photo' => 'business/photos/old.jpg']);

        $response = $this->put(route('admin.businesses.update', $business), [
            ...$this->businessData(), 'photo' => UploadedFile::fake()->image('replacement.png'),
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('admin.businesses.show', $business));
        $business->refresh();
        $this->assertNotSame('business/photos/old.jpg', $business->photo);
        Storage::disk('public')->assertExists($business->photo);
        Storage::disk('public')->assertMissing('business/photos/old.jpg');
    }

    public function test_edit_without_a_photo_keeps_the_existing_photo(): void
    {
        Storage::disk('public')->put('business/photos/old.jpg', 'old photo');
        $business = Business::create([...$this->businessData(), 'photo' => 'business/photos/old.jpg']);
        $this->put(route('admin.businesses.update', $business), [...$this->businessData(), 'photo' => null])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('business/photos/old.jpg', $business->fresh()->photo);
        Storage::disk('public')->assertExists('business/photos/old.jpg');
    }

    public function test_invalid_upload_keeps_the_existing_photo(): void
    {
        Storage::disk('public')->put('business/photos/old.jpg', 'old photo');
        $business = Business::create([...$this->businessData(), 'photo' => 'business/photos/old.jpg']);
        $this->put(route('admin.businesses.update', $business), [
            ...$this->businessData(), 'photo' => UploadedFile::fake()->create('invalid.txt', 10, 'text/plain'),
        ])->assertSessionHasErrors('photo');
        $this->assertSame('business/photos/old.jpg', $business->fresh()->photo);
        Storage::disk('public')->assertExists('business/photos/old.jpg');
    }

    public function test_failed_photo_storage_keeps_the_existing_photo(): void
    {
        $disk = Storage::disk('public');
        $disk->put('business/photos/old.jpg', 'old photo');
        $business = Business::create([...$this->businessData(), 'photo' => 'business/photos/old.jpg']);
        $failedDisk = \Mockery::mock(\Illuminate\Filesystem\FilesystemAdapter::class);
        $failedDisk->shouldReceive('putFileAs')->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('public')->andReturn($failedDisk);

        $this->put(route('admin.businesses.update', $business), [
            ...$this->businessData(), 'photo' => UploadedFile::fake()->image('replacement.jpg'),
        ])->assertSessionHasErrors('photo');

        $this->assertSame('business/photos/old.jpg', $business->fresh()->photo);
        $disk->assertExists('business/photos/old.jpg');
    }

    public static function reportTypes(): array
    {
        return [['users'], ['businesses'], ['matrimony'], ['payments']];
    }

    #[DataProvider('reportTypes')]
    public function test_reports_download_with_legacy_null_dates(string $type): void
    {
        DB::table('users')->insert(['id' => 2, 'name' => 'Legacy User', 'email' => 'legacy@example.test']);
        DB::table('businesses')->insert($this->businessData());
        DB::table('matrimony_profiles')->insert(['user_id' => 2]);
        DB::table('payments')->insert(['user_id' => 2, 'amount' => 10]);

        $response = $this->get(route('admin.reports.download', $type));
        $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('attachment;', $response->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
        $this->assertStringNotContainsString('/Encrypt', $response->getContent());
    }

    public static function xlsReportTypes(): array
    {
        return [
            ['users', 'Admin Notes'],
            ['businesses', 'Contact Email'],
            ['matrimony', 'Partner Preferences'],
            ['payments', 'Payment Method'],
        ];
    }

    #[DataProvider('xlsReportTypes')]
    public function test_reports_download_as_direct_unprotected_xls_with_complete_headers(string $type, string $expectedHeader): void
    {
        DB::table('users')->insert([
            'id' => 2,
            'name' => 'Legacy User',
            'email' => 'legacy@example.test',
            'phone' => '919876543210',
            'created_at' => '2026-09-04 10:00:00',
        ]);
        DB::table('businesses')->insert($this->businessData());
        DB::table('matrimony_profiles')->insert(['user_id' => 2]);
        DB::table('payments')->insert(['user_id' => 2, 'amount' => 10]);

        $response = $this->get(route('admin.reports.download.xls', $type));
        $response->assertOk()->assertHeader('Content-Type', 'application/vnd.ms-excel');
        $disposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('.xls', $disposition);
        $this->assertStringNotContainsString('.zip', $disposition);

        $content = $response->streamedContent();
        $this->assertStringStartsWith("\xD0\xCF\x11\xE0", $content);

        $path = tempnam(sys_get_temp_dir(), 'report_test_');
        file_put_contents($path, $content);
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $headers = $sheet->rangeToArray('A3:' . $sheet->getHighestColumn() . '3')[0];

            $this->assertContains($expectedHeader, $headers);
            $this->assertNotTrue($sheet->getProtection()->getSheet());
            if ($type === 'users') {
                $this->assertSame('919876543210', $sheet->getCell('D4')->getValue());
            }

            $spreadsheet->disconnectWorksheets();
        } finally {
            @unlink($path);
        }
    }

    #[DataProvider('reportTypes')]
    public function test_empty_filtered_reports_download(string $type): void
    {
        $this->get(route('admin.reports.download', [
            'type' => $type, 'start_date' => '2000-01-01', 'end_date' => '2000-01-02',
        ]))->assertOk()->assertHeader('Content-Type', 'application/pdf');
    }
}
