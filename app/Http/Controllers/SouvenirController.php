<?php

namespace App\Http\Controllers;

use App\Models\Souvenir;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SouvenirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $souvenirs = $user->souvenirs()->with('users')->get();

        if ($souvenirs->isEmpty()) {
            return response()->json(['message' => 'Souvenir introuvable'], 404);
        }

        return response()->json($souvenirs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $request->validate([
            'name' => 'required',
            'description',
            'cover_image',
        ]);

        $souvenir = Souvenir::create([
            'name' => $request->name,
            'description' => $request->description,
            'cover_image' => $request->cover_image,
            'is_closed' => false
        ]);

        $souvenir->users()->attach($user->id, [
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        return response()->json($souvenir, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $user = $request->user();
        $souvenir = $user->souvenirs()->with('users')->find($id);

        if (!$souvenir) {
            return response()->json(['message' => 'Souvenir introuvable'], 404);
        }

        return response()->json($souvenir);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $souvenir = $user->souvenirs()->with('users')->find($id);

        if (!$souvenir) {
            return response()->json(['message' => 'Souvenir introuvable'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'cover_image' => 'sometimes|nullable|string',
            'is_closed' => 'sometimes|boolean',
            'users' => 'sometimes|array', // si tu modifies les rôles
        ]);

        $souvenir->update($validated);

        return response()->json($souvenir, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Souvenir $souvenir): JsonResponse
    {
        $souvenir->delete();

        return response()->json(['message' => 'souvenir supprimé']);
    }
}
