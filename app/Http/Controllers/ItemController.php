<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Service\ItemService;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
{
    public function __construct(protected ItemService $itemService)
    { }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $item = $this->itemService->all();
    
        if($item->isEmpty()) {
            return response()->json([
                'message' => 'Nenhum item encontrado!',
                'items' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Itens encontrados com sucesso!',
            'items' => $item
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ItemRequest $request): JsonResponse
    {
        $item = $this->itemService->create($request->validated());

        return response()->json([
            'message' => 'Item criado com sucesso!',
            'item' => $item
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $item = $this->itemService->find($id);

        if(!$item) {
            return response()->json([
                'message' => 'Item não encontrado!',
                'item' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Item encontrado com sucesso!',
            'item' => $item
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ItemRequest $request, int $id): JsonResponse
    {
        $item = $this->itemService->update($request->validated(), $id);

        if(!$item) {
            return response()->json([
                'message' => 'Erro ao atualizar item!',
                'item' => null
            ], 500);
        }

        return response()->json([
            'message' => 'Item atualizado com sucesso!',
            'item' => $item
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $item = $this->itemService->delete($id);

        if(!$item) {
            return response()->json([
                'message' => 'Erro ao deletar item!',
                'item' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Item deletado com sucesso!',
            'item' => $item
        ], 200);
    }
}
