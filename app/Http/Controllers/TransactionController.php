<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Portfolio;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransactionController extends Controller
{
    public function index(Portfolio $portfolio): AnonymousResourceCollection
    {
        $this->authorize('viewAny', [Transaction::class, $portfolio]);

        $transactions = $portfolio->transactions()->latest('executed_at')->get();

        return TransactionResource::collection($transactions);
    }

    public function store(StoreTransactionRequest $request, Portfolio $portfolio): JsonResponse
    {
        $this->authorize('create', [Transaction::class, $portfolio]);

        $transaction = $portfolio->transactions()->create($request->validated());

        return (new TransactionResource($transaction))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Transaction $transaction): TransactionResource
    {
        $transaction->loadMissing('portfolio');

        $this->authorize('view', $transaction);

        return new TransactionResource($transaction);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): TransactionResource
    {
        $transaction->loadMissing('portfolio');

        $this->authorize('update', $transaction);

        $transaction->update($request->validated());

        return new TransactionResource($transaction);
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->loadMissing('portfolio');

        $this->authorize('delete', $transaction);

        $transaction->delete();

        return response()->json(null, 204);
    }
}
