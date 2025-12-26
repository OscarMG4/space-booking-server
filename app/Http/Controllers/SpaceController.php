<?php

namespace App\Http\Controllers;

use App\Http\Requests\Space\StoreSpaceRequest;
use App\Http\Requests\Space\UpdateSpaceRequest;
use App\Http\Resources\SpaceResource;
use App\Services\SpaceService;
use Illuminate\Http\Request;

class SpaceController extends Controller
{
    protected $spaceService;

    public function __construct(SpaceService $spaceService)
    {
        $this->spaceService = $spaceService;
    }
    public function index(Request $request)
    {
        $filters = $request->only(['type', 'is_available', 'min_capacity', 'max_price', 'search']);
        $perPage = $request->get('per_page', 15);
        
        $spaces = $this->spaceService->getSpaces($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => SpaceResource::collection($spaces)->response()->getData(true)
        ]);
    }

    public function store(StoreSpaceRequest $request)
    {
        $space = $this->spaceService->createSpace($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Espacio creado exitosamente',
            'data' => new SpaceResource($space)
        ], 201);
    }

    public function show($id)
    {
        $space = $this->spaceService->getSpaceWithRelations($id);

        if (!$space) {
            return response()->json([
                'success' => false,
                'message' => 'Espacio no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new SpaceResource($space)
        ]);
    }

    public function update(UpdateSpaceRequest $request, $id)
    {
        $space = $this->spaceService->getSpaceWithRelations($id);

        if (!$space) {
            return response()->json([
                'success' => false,
                'message' => 'Espacio no encontrado'
            ], 404);
        }

        $space = $this->spaceService->updateSpace($space, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Espacio actualizado exitosamente',
            'data' => new SpaceResource($space)
        ]);
    }

    public function destroy($id)
    {
        $space = $this->spaceService->getSpaceWithRelations($id);

        if (!$space) {
            return response()->json([
                'success' => false,
                'message' => 'Espacio no encontrado'
            ], 404);
        }

        try {
            $this->spaceService->deleteSpace($space);

            return response()->json([
                'success' => true,
                'message' => 'Espacio eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
