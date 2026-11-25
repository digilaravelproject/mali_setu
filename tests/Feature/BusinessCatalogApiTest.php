<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

class BusinessCatalogApiTest extends TestCase
{
    private User $user;
    private Business $business;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('database.default', 'catalog_testing');
        config()->set('database.connections.catalog_testing', [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]);
        DB::purge('catalog_testing');
        DB::setDefaultConnection('catalog_testing');

        Schema::create('users', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('email');
            $table->string('phone')->nullable(); $table->string('photo')->nullable();
            $table->string('password')->nullable(); $table->timestamps();
        });
        Schema::create('businesses', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id'); $table->string('business_name'); $table->timestamps();
        });
        Schema::create('caste_certificates', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id'); $table->string('file_path')->nullable(); $table->timestamps();
        });
        foreach (['products', 'services'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id(); $table->unsignedBigInteger('business_id'); $table->string('name');
                $table->text('description'); $table->decimal('cost', 10, 2)->nullable();
                $table->string('image_path', 500)->nullable(); $table->json('image_paths')->nullable();
                $table->string('status')->default('active'); $table->timestamps();
            });
        }
        Schema::create('business_enquiries', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id'); $table->unsignedBigInteger('business_id');
            $table->string('enquirable_type')->nullable(); $table->unsignedBigInteger('enquirable_id')->nullable();
            $table->decimal('price', 12, 2)->nullable(); $table->text('description');
            $table->string('status')->default('new'); $table->timestamps();
        });

        $this->user = User::create(['name' => 'Owner', 'email' => 'owner@example.test']);
        $this->business = Business::forceCreate(['user_id' => $this->user->id, 'business_name' => 'Garden Store']);
        $this->actingAs($this->user, 'sanctum');
        Storage::fake('public');
        $this->mock(NotificationService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createNotification')->zeroOrMoreTimes()->andReturn(null);
        });
    }

    public function test_owner_can_add_update_and_delete_product_with_multiple_images(): void
    {
        $created = $this->post('/api/business/products', [
            'business_id' => $this->business->id,
            'name' => 'Fertilizer', 'description' => 'Organic fertilizer', 'cost' => 499,
            'images' => [UploadedFile::fake()->image('one.jpg'), UploadedFile::fake()->image('two.jpg')],
        ])->assertCreated()->json('data.product');

        $product = Product::findOrFail($created['id']);
        $this->assertCount(2, $product->image_paths);
        foreach ($product->image_paths as $path) {
            Storage::disk('public')->assertExists($path);
        }

        $oldPath = $product->image_paths[0];
        $this->post('/api/business/products/'.$product->id, [
            '_method' => 'PUT', 'name' => 'Updated Fertilizer',
            'remove_image_paths' => [$oldPath],
            'images' => [UploadedFile::fake()->image('three.jpg')],
        ])->assertOk()->assertJsonPath('data.product.name', 'Updated Fertilizer');
        Storage::disk('public')->assertMissing($oldPath);

        $paths = $product->fresh()->image_paths;
        $this->deleteJson('/api/business/products/'.$product->id)->assertOk();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        foreach ($paths as $path) {
            Storage::disk('public')->assertMissing($path);
        }
    }

    public function test_owner_can_update_product_and_service_from_business_console(): void
    {
        Storage::disk('public')->put('products/old.jpg', 'old product');
        Storage::disk('public')->put('services/old.jpg', 'old service');
        $product = Product::create([
            'business_id' => $this->business->id,
            'name' => 'Old Product', 'description' => 'Old description', 'cost' => 10,
            'image_path' => 'products/old.jpg',
        ]);
        $service = Service::create([
            'business_id' => $this->business->id,
            'name' => 'Old Service', 'description' => 'Old description', 'cost' => 20,
            'image_path' => 'services/old.jpg',
        ]);

        $this->put('/dashboard/business/products/'.$product->id, [
            'product_id' => $product->id,
            'name' => 'Updated Product', 'description' => 'Updated product description', 'cost' => 99,
            'image' => UploadedFile::fake()->image('new-product.jpg'),
        ])->assertRedirect('/dashboard/business?business_id='.$this->business->id);

        $this->put('/dashboard/business/services/'.$service->id, [
            'service_id' => $service->id,
            'name' => 'Updated Service', 'description' => 'Updated service description', 'cost' => 199,
            'image' => UploadedFile::fake()->image('new-service.jpg'),
        ])->assertRedirect('/dashboard/business?business_id='.$this->business->id);

        $this->assertSame('Updated Product', $product->fresh()->name);
        $this->assertSame('Updated Service', $service->fresh()->name);
        Storage::disk('public')->assertMissing('products/old.jpg');
        Storage::disk('public')->assertMissing('services/old.jpg');
        Storage::disk('public')->assertExists($product->fresh()->image_path);
        Storage::disk('public')->assertExists($service->fresh()->image_path);
    }

    public function test_user_can_create_and_read_an_enquiry_for_another_users_product(): void
    {
        $buyer = User::create(['name' => 'Buyer', 'email' => 'buyer@example.test', 'phone' => '9999999999']);
        $product = Product::create([
            'business_id' => $this->business->id, 'name' => 'Garden Kit',
            'description' => 'Complete kit', 'cost' => 2500, 'status' => 'active',
        ]);
        $this->actingAs($buyer, 'sanctum');

        $id = $this->postJson('/api/business/enquiries', [
            'product_id' => $product->id,
            'price' => 2200,
            'description' => 'Please confirm availability for two kits.',
        ])->assertCreated()
            ->assertJsonPath('data.enquiry.user.name', 'Buyer')
            ->assertJsonPath('data.enquiry.enquirable.name', 'Garden Kit')
            ->json('data.enquiry.id');

        $this->getJson('/api/business/enquiries/'.$id)
            ->assertOk()
            ->assertJsonPath('data.enquiry.business.business_name', 'Garden Store')
            ->assertJsonPath('data.enquiry.price', '2200.00');

        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/business/enquiries/'.$id)
            ->assertOk()
            ->assertJsonPath('data.enquiry.user.email', 'buyer@example.test');
    }
}
