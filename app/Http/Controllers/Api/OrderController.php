<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatusesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = isset(request()->page) ? Order::paginate(request()->pagination ?? 10) : Order::all();
        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
        $validated = $request->validated();

        [$products, $total] = $this->processOrder($validated);

        $order = Order::create([
            'user_id' => auth()->id(),
            'status' => OrderStatusesEnum::CREATED->value,
            'total' => $total,
        ]);
        $syncChanges = $order->products()->sync($products);
        $this->processProductsQuantity($syncChanges, $order);

        return response()->json(new OrderResource($order));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderRequest $request, Order $order)
    {
        $validated = $request->validated();
        $orderProducts = $order->products->keyBy('id');

        [$products, $total] = $this->processOrder($validated);

        $order->update([
            'total' => $total,
        ]);
        $syncChanges = $order->products()->sync($products);
        $this->processProductsQuantity($syncChanges, $order, $orderProducts);

        return response()->json(new OrderResource($order));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([
            'data' => [
                'status' => 'success',
                'message' => 'Order deleted successfully!',
            ],
        ]);
    }

    /**
     * Process order by calculating total price for order
     */
    private function processOrder(array $validated) {
        $products = [];
        $total = 0;

        $productsId = collect($validated['products'])->pluck('id');

        $modelProducts = Product::select('id', 'price', 'stock')
            ->whereIn('id', $productsId)
            ->get()
            ->keyBy('id');

        foreach ($validated['products'] as $product) {
            $modelProduct = $modelProducts[$product['id']];

            $products[$product['id']] = [
                'price' => $modelProduct->price,
                'quantity' => $product['quantity'],
            ];

            $total += $products[$product['id']]['price'] * $products[$product['id']]['quantity'];
        }

        return [$products, $total];
    }

    /**
     * Calculates changes in product stock
     */
    private function processProductsQuantity(array $syncChanges, Order $order, Collection $orderProducts = null) {
        $pivotProducts = $order->products()->get();

        foreach($syncChanges as $key => $syncChangedProducts) {
            if ($key === 'attached') {
                $products = $pivotProducts->whereIn('id', $syncChangedProducts);

                foreach ($products as $product) {
                    $product->decrement('stock', $product->pivot->quantity);
                }
            }

            if ($key === 'detached') {
                $products = Product::whereIn('id', $syncChangedProducts)->get();

                foreach ($products as $product) {
                    $product->increment('stock', $orderProducts[$product->id]->pivot->quantity);
                }
            }

            if ($key === 'updated') {
                $products = $pivotProducts->whereIn('id', $syncChangedProducts);

                foreach ($products as $product) {
                    $pivotProductQuantity = $product->pivot->quantity;

                    $quantityDifference = $orderProducts[$product->id]->pivot->quantity - $pivotProductQuantity;

                    if ($quantityDifference !== 0) {
                        $product->increment('stock', $quantityDifference);
                    }
                }
            }
        }
    }
}
