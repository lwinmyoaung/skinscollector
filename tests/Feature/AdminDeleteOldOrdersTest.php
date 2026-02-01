<?php

namespace Tests\Feature;

use App\Models\KpayOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Carbon\Carbon;

class AdminDeleteOldOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_old_orders_and_images()
    {
        Storage::fake('public');

        // Create admin user
        $admin = User::factory()->create([
            'role' => 'admin',
            'phone' => '09999999999', // Ensure unique phone if required
        ]);

        // Create an old order with image
        $oldFile = UploadedFile::fake()->create('old_transaction.txt', 100);
        // Simulate storage
        $oldFilename = 'old_transaction.txt';
        Storage::disk('public')->put('topups/' . $oldFilename, $oldFile->getContent());

        $oldOrder = KpayOrder::create([
            'user_id' => 1,
            'game_type' => 'mlbb',
            'product_id' => '123',
            'product_name' => 'Diamonds',
            'player_id' => '12345',
            'payment_method' => 'kpay',
            'kpay_phone' => '09123456789',
            'amount' => 1000,
            'transaction_image' => $oldFilename,
            'status' => 'pending',
        ]);

        // Manually update created_at using query builder to ensure it persists
        KpayOrder::where('id', $oldOrder->id)->update(['created_at' => Carbon::now()->subDays(10)]);

        // Create a new order with image (should NOT be deleted)
        $newFile = UploadedFile::fake()->create('new_transaction.txt', 100);
        $newFilename = 'new_transaction.txt';
        Storage::disk('public')->put('topups/' . $newFilename, $newFile->getContent());

        $newOrder = KpayOrder::create([
            'user_id' => 1,
            'game_type' => 'mlbb',
            'product_id' => '123',
            'product_name' => 'Diamonds',
            'player_id' => '12345',
            'payment_method' => 'kpay',
            'kpay_phone' => '09123456789',
            'amount' => 1000,
            'transaction_image' => $newFilename,
            'status' => 'pending',
        ]);
        // No need to change created_at for new order as it defaults to now


        // Assert files exist before deletion
        Storage::disk('public')->assertExists('topups/' . $oldFilename);
        Storage::disk('public')->assertExists('topups/' . $newFilename);

        // Perform deletion of orders older than 5 days
        $response = $this->actingAs($admin)
                         ->delete(route('admin.confirm.orders.delete_old'), [
                             'days' => 5
                         ]);

        $response->assertSessionHas('success');

        // Assert old order is gone
        $this->assertDatabaseMissing('kpay_orders', ['id' => $oldOrder->id]);
        // Assert old image is gone
        Storage::disk('public')->assertMissing('topups/' . $oldFilename);

        // Assert new order still exists
        $this->assertDatabaseHas('kpay_orders', ['id' => $newOrder->id]);
        // Assert new image still exists
        Storage::disk('public')->assertExists('topups/' . $newFilename);
    }
}
