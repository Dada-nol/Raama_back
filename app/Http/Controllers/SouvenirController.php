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
            'cover_image' => 'nullable|file|mimes:png,jpg,jpeg|max:10240',
        ]);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('souvenirs/covers', 'public');
        }

        $souvenir = Souvenir::create([
            'user_id' => $user->id,
            'memory_type_id' => $request->memory_type,
            'title' => $request->title,
            'cover_image' => $path ?? null,
            'memory_points' => 0
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
        $souvenir = $user->souvenirs()->with(['entries', 'users'])->findOrFail($id);

        $souvenir->users()->updateExistingPivot($user->id, [
            'last_visited_at' => now()
        ]);

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

        $role = $souvenir->users
            ->firstWhere('id', $user->id)?->pivot->role;

        if ($role !== "admin") {
            return response()->json(['message' => 'Vous n\'avez pas les permissions nécessaire pour faire cela'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'cover_image' => 'sometimes|nullable|file|mimes:png,jpg,jpeg|max:10240',
            'users' => 'sometimes|array',
        ]);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('souvenirs', 'public');
            $validated['cover_image'] = $path;
        }

        $souvenir->update($validated);

        return response()->json([
            'id' => $souvenir->id,
            'title' => $souvenir->title,
            'cover_image' => $souvenir->cover_image,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, Souvenir $souvenir): JsonResponse
    {
        $user = $request->user();

        $souvenir->load('users');

        $role = $souvenir->users
            ->firstWhere('id', $user->id)?->pivot->role;

        if ($role !== "admin") {
            return response()->json(['message' => 'Vous n\'avez pas les permissions nécessaire pour faire cela'], 403);
        }
        $souvenir->delete();

        return response()->json(['message' => 'souvenir supprimé']);
    }

    public function recent(Request $request): JsonResponse
    {
        $user = $request->user();
        // $recent = $user->souvenirs()->where('updated_at', '>=', now()->subDays(7))->latest()->take(3)->get();

        $recent = $user->souvenirs()
            ->orderByPivot('last_visited_at', 'desc')
            ->take(3)
            ->get();

        return response()->json($recent);
    }
}
