<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Service\ItemService;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;

class ItemController extends Controller
{
    use ApiResponse;

    public function __construct(protected ItemService $itemService)
    { }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $item = $this->itemService->paginate(10);

        return $this->successResponse([
            'items' => $item,
            'summary' => $this->itemService->getSummary()
        ], 'Itens encontrados com sucesso!');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ItemRequest $request): JsonResponse
    {
        $item = $this->itemService->create($request->validated());

        return $this->successResponse($item, 'Item criado com sucesso!', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $item = $this->itemService->find($id);

        if(!$item) {
            return $this->errorResponse('Item não encontrado!', 404);
        }

        return $this->successResponse($item, 'Item encontrado com sucesso!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ItemRequest $request, int $id): JsonResponse
    {
        $item = $this->itemService->update($request->validated(), $id);

        if(!$item) {
            return $this->errorResponse('Erro ao atualizar item!', 404);
        }

        return $this->successResponse($item, 'Item atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->itemService->delete($id);

        if(!$deleted) {
            return $this->errorResponse('Erro ao deletar item!', 404);
        }

        return $this->successResponse(null, 'Item deletado com sucesso!');
    }
}
