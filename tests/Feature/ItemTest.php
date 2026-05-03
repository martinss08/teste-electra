<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_store_item()
    {
        $response = $this->post(route('items.store'), [
            'name' => 'Item Teste',
            'description' => 'Descrição teste',
            'price' => 10.5,
            'quantity' => 2,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('items', [
            'name' => 'Item Teste',
            'description' => 'Descrição teste',
            'price' => 10.5,
            'quantity' => 2,
        ]);
    }

    public function test_show_item()
    {
        $item = Item::factory()->create();

        $response = $this->get(route('items.show', $item->id));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'item' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'quantity',
                ]
            ])
            ->assertJsonFragment([
                'id' => $item->id,
                'name' => $item->name,
            ]);
    }

    // public function test_show_item_not_found()
    // {
    //     $response = $this->get(route('items.show', 999));

    //     $response
    //         ->assertStatus(404)
    //         ->assertJson([
    //             'message' => 'Item não encontrado!',
    //             'item' => null
    //         ]);
    // }

    public function test_update_item()
    {
        $item = Item::factory()->create();

        $data = [
            'name' => 'Updated Item',
            'quantity' => 10,
            'description' => 'Updated description',
            'price' => 99.99,
        ];

        $response = $this->put(route('items.update', $item->id), $data);

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Item'
            ]);

         $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Updated Item',
            'quantity' => 10,
            'price' => 99.99,
        ]);
    }

    // public function test_update_item_not_found()
    // {
    //     $response = $this->put(route('items.update', 999), [
    //         'name' => 'Teste',
    //         'price' => 10,
    //         'quantity' => 1,
    //     ]);

    //     $response->assertStatus(500);
    // }

    public function test_delete_item()
    {
        $item = Item::factory()->create();

        $response = $this->delete(route('items.destroy', $item->id));

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Item deletado com sucesso!',
            ]);

        $this->assertDatabaseMissing('items', [
            'id' => $item->id
        ]);
    }

    // public function test_delete_item_not_found()
    // {
    //     $response = $this->delete(route('items.destroy', 999));

    //     $response->assertStatus(404);
    // }

   public function test_create_item_validation()
    {
        $response = $this->post(route('items.store'), []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'quantity'])
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'name',
                    'price',
                    'quantity'
                ]
            ]);
    }
}
