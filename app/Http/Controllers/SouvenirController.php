<?php

namespace App\Http\Controllers;

use App\Models\MemoryType;
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
            'memory_type' => 'required|exists:memory_types,id',
            'title' => 'required',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|file|mimes:png,jpg,jpeg|max:5120',
        ]);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('souvenirs/cover', 'public');
        }

        $souvenir = Souvenir::create([
            'user_id' => $user->id,
            'memory_type_id' => $request->memory_type,
            'title' => $request->title,
            'description' => $request->description,
            'cover_image' => $path ?? null,
            'memory_points' => 0
        ]);

        $souvenir->users()->attach($user->id, [
            'role' => 'admin',
            'joined_at' => now(),
            'can_edit' => true
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

    public function recent(): JsonResponse
    {
        $recent = Souvenir::where('updated_at', '>=', now()->subDays(7))->latest()->take(5)->get();

        return response()->json($recent);
    }
}
