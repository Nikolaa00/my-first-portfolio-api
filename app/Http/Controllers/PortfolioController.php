<?php

namespace App\Http\Controllers;

use App\Http\Requests\Portfolio\StorePortfolioRequest;
use App\Http\Requests\Portfolio\UpdatePortfolioRequest;
use App\Http\Resources\PortfolioResource;
use App\Models\Portfolio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PortfolioController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Portfolio::class);

        $query = Portfolio::query()->latest();

        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        return PortfolioResource::collection($query->get());
    }

    public function store(StorePortfolioRequest $request): JsonResponse
    {
        $this->authorize('create', Portfolio::class);

        $portfolio = Portfolio::query()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return (new PortfolioResource($portfolio))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Portfolio $portfolio): PortfolioResource
    {
        $this->authorize('view', $portfolio);

        return new PortfolioResource($portfolio);
    }

    public function update(UpdatePortfolioRequest $request, Portfolio $portfolio): PortfolioResource
    {
        $this->authorize('update', $portfolio);

        $portfolio->update($request->validated());

        return new PortfolioResource($portfolio);
    }

    public function destroy(Portfolio $portfolio): JsonResponse
    {
        $this->authorize('delete', $portfolio);

        $portfolio->delete();

        return response()->json(null, 204);
    }
}
