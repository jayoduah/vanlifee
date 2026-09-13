<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use App\Models\Van;
use App\Models\VanImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VanRentalApiTest extends TestCase
{
    use RefreshDatabase;

    private User $owner1;
    private User $owner2;
    private User $customer1;
    private User $customer2;
    private Van $van1;
    private Van $van2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner1 = User::create([
            'name' => 'Owner One',
            'email' => 'owner1@example.com',
            'phone_number' => '1111111111',
            'password' => bcrypt('password123'),
            'role' => 'owner',
        ]);

        $this->owner2 = User::create([
            'name' => 'Owner Two',
            'email' => 'owner2@example.com',
            'phone_number' => '2222222222',
            'password' => bcrypt('password123'),
            'role' => 'owner',
        ]);

        $this->customer1 = User::create([
            'name' => 'Customer One',
            'email' => 'cust1@example.com',
            'phone_number' => '3333333333',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);

        $this->customer2 = User::create([
            'name' => 'Customer Two',
            'email' => 'cust2@example.com',
            'phone_number' => '4444444444',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);

        $this->van1 = Van::create([
            'user_id' => $this->owner1->id,
            'make' => 'Ford',
            'model' => 'Transit',
            'year' => 2023,
            'price_per_day' => 85.00,
            'description' => 'Owner 1 van',
        ]);

        $this->van2 = Van::create([
            'user_id' => $this->owner2->id,
            'make' => 'Mercedes',
            'model' => 'Sprinter',
            'year' => 2024,
            'price_per_day' => 120.00,
            'description' => 'Owner 2 van',
        ]);
    }

    public function test_user_can_register_as_customer_and_owner(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'phone_number' => '555-1234',
            'role' => 'customer',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'user', 'token', 'token_type']);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'role' => 'customer',
        ]);
    }

    public function test_user_login_success_and_failure(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'owner1@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user']);

        $invalidResponse = $this->postJson('/api/login', [
            'email' => 'owner1@example.com',
            'password' => 'wrongpassword',
        ]);

        $invalidResponse->assertStatus(401);
    }

    public function test_authenticated_user_profile_and_logout(): void
    {
        $token = $this->owner1->createToken('test_token')->plainTextToken;

        $profileResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/me');

        $profileResponse->assertStatus(200)
            ->assertJsonPath('user.email', 'owner1@example.com');

        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $logoutResponse->assertStatus(200);
    }

    public function test_anyone_can_browse_vans_and_view_details(): void
    {
        $listResponse = $this->getJson('/api/vans');
        $listResponse->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'make', 'model']]]);

        $showResponse = $this->getJson('/api/vans/' . $this->van1->id);
        $showResponse->assertStatus(200)
            ->assertJsonPath('van.make', 'Ford');
    }

    public function test_van_owner_can_create_van(): void
    {
        $token = $this->owner1->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/vans', [
                'make' => 'Toyota',
                'model' => 'HiAce',
                'year' => 2022,
                'price_per_day' => 90.00,
                'description' => 'Spacious and comfortable',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('van.make', 'Toyota')
            ->assertJsonPath('van.user_id', $this->owner1->id);
    }

    public function test_customer_cannot_create_van(): void
    {
        $token = $this->customer1->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/vans', [
                'make' => 'Toyota',
                'model' => 'HiAce',
                'year' => 2022,
            ]);

        $response->assertStatus(403);
    }

    public function test_owner_can_update_their_own_van(): void
    {
        $token = $this->owner1->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/vans/' . $this->van1->id, [
                'make' => 'Ford Updated',
                'model' => 'Transit Custom',
                'year' => 2023,
                'price_per_day' => 95.00,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('van.make', 'Ford Updated');
    }

    public function test_owner_cannot_update_another_owners_van(): void
    {
        $token = $this->owner2->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/vans/' . $this->van1->id, [
                'make' => 'Hacked Make',
            ]);

        $response->assertStatus(403);
    }

    public function test_customer_cannot_update_any_van(): void
    {
        $token = $this->customer1->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/vans/' . $this->van1->id, [
                'make' => 'Customer Change',
            ]);

        $response->assertStatus(403);
    }

    public function test_owner_can_delete_own_van(): void
    {
        $token = $this->owner1->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/vans/' . $this->van1->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('vans', ['id' => $this->van1->id]);
    }

    public function test_owner_cannot_delete_another_owners_van(): void
    {
        $token = $this->owner2->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/vans/' . $this->van1->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('vans', ['id' => $this->van1->id]);
    }

    public function test_owner_can_upload_and_delete_images_for_own_van(): void
    {
        Storage::fake('public');
        $token = $this->owner1->createToken('test')->plainTextToken;
        $file = UploadedFile::fake()->image('van.jpg');

        $uploadResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/vans/' . $this->van1->id . '/images', [
                'image' => $file,
                'is_primary' => true,
            ]);

        $uploadResponse->assertStatus(201)
            ->assertJsonStructure(['image' => ['id', 'image_path', 'url', 'is_primary']]);

        $imageId = $uploadResponse->json('image.id');
        $this->assertDatabaseHas('van_images', ['id' => $imageId, 'van_id' => $this->van1->id]);

        // Delete image
        $deleteResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/van-images/' . $imageId);

        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('van_images', ['id' => $imageId]);
    }

    public function test_owner_cannot_upload_image_to_another_owners_van(): void
    {
        Storage::fake('public');
        $token = $this->owner2->createToken('test')->plainTextToken;
        $file = UploadedFile::fake()->image('van.jpg');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/vans/' . $this->van1->id . '/images', [
                'image' => $file,
            ]);

        $response->assertStatus(403);
    }

    public function test_customer_cannot_upload_images(): void
    {
        Storage::fake('public');
        $token = $this->customer1->createToken('test')->plainTextToken;
        $file = UploadedFile::fake()->image('van.jpg');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/vans/' . $this->van1->id . '/images', [
                'image' => $file,
            ]);

        $response->assertStatus(403);
    }

    public function test_customer_can_review_van(): void
    {
        $token = $this->customer1->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/vans/' . $this->van1->id . '/reviews', [
                'rating' => 5,
                'comment' => 'Excellent service and great ride!',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('review.rating', 5);

        $this->assertDatabaseHas('reviews', [
            'van_id' => $this->van1->id,
            'user_id' => $this->customer1->id,
            'rating' => 5,
        ]);
    }

    public function test_owner_cannot_review_own_van(): void
    {
        $token = $this->owner1->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/vans/' . $this->van1->id . '/reviews', [
                'rating' => 5,
                'comment' => 'Self praise review',
            ]);

        $response->assertStatus(403);
    }

    public function test_customer_cannot_review_same_van_twice(): void
    {
        $token = $this->customer1->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/vans/' . $this->van1->id . '/reviews', [
                'rating' => 5,
                'comment' => 'First review',
            ])->assertStatus(201);

        $secondResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/vans/' . $this->van1->id . '/reviews', [
                'rating' => 4,
                'comment' => 'Duplicate attempt',
            ]);

        $secondResponse->assertStatus(403);
    }

    public function test_customer_cannot_edit_another_customers_review(): void
    {
        $review = Review::create([
            'van_id' => $this->van1->id,
            'user_id' => $this->customer1->id,
            'rating' => 4,
            'comment' => 'Nice van',
        ]);

        $token2 = $this->customer2->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token2)
            ->putJson('/api/reviews/' . $review->id, [
                'comment' => 'Attempted hijack of review',
            ]);

        $response->assertStatus(403);
    }
}
