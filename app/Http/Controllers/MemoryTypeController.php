<?php

namespace App\Http\Controllers;

use App\Models\MemoryType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemoryTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $memory_type = MemoryType::all();

        if ($memory_type->isEmpty()) {
            return response()->json(['message' => 'Memory type introuvable'], 404);
        }

        return response()->json($memory_type);
    }
}
