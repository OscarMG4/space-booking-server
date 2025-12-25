<?php

namespace App\Http\Controllers;

use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SpaceController extends Controller
{
    public function index(Request $request)
    {
        $query = Space::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_available')) {
            $query->where('is_available', filter_var($request->is_available, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->has('min_capacity')) {
            $query->where('capacity', '>=', $request->min_capacity);
        }

        if ($request->has('max_price')) {
            $query->where('price_per_hour', '<=', $request->max_price);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $spaces = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $spaces
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|in:sala_reuniones,oficina,auditorio,laboratorio,espacio_coworking,otro',
            'capacity' => 'required|integer|min:1',
            'price_per_hour' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'floor' => 'nullable|string|max:50',
            'amenities' => 'nullable|array',
            'image_url' => 'nullable|url',
            'is_available' => 'boolean',
            'rules' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $space = Space::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Espacio creado exitosamente',
            'data' => $space
        ], 201);
    }

    public function show($id)
    {
        $space = Space::find($id);

        if (!$space) {
            return response()->json([
                'success' => false,
                'message' => 'Espacio no encontrado'
            ], 404);
        }

        $space->load('reviews', 'availabilities');

        return response()->json([
            'success' => true,
            'data' => $space
        ]);
    }

    public function update(Request $request, $id)
    {
        $space = Space::find($id);

        if (!$space) {
            return response()->json([
                'success' => false,
                'message' => 'Espacio no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'type' => 'sometimes|required|string|in:sala_reuniones,oficina,auditorio,laboratorio,espacio_coworking,otro',
            'capacity' => 'sometimes|required|integer|min:1',
            'price_per_hour' => 'sometimes|required|numeric|min:0',
            'location' => 'sometimes|required|string|max:255',
            'floor' => 'nullable|string|max:50',
            'amenities' => 'nullable|array',
            'image_url' => 'nullable|url',
            'is_available' => 'boolean',
            'rules' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $space->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Espacio actualizado exitosamente',
            'data' => $space
        ]);
    }

    public function destroy($id)
    {
        $space = Space::find($id);

        if (!$space) {
            return response()->json([
                'success' => false,
                'message' => 'Espacio no encontrado'
            ], 404);
        }

        $activeBookings = $space->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('end_time', '>', now())
            ->count();

        if ($activeBookings > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el espacio porque tiene reservas activas'
            ], 422);
        }

        $space->delete();

        return response()->json([
            'success' => true,
            'message' => 'Espacio eliminado exitosamente'
        ]);
    }
}
