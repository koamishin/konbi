<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Products/Index', [
            'products' => $products
        ]);
    }

    public function create()
    {
        return Inertia::render('Products/Create', [
            'categories' => Category::all(),
            'brands' => Brand::all(),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        
        // Auto-assign store_id if user is scoped to a store
        if ($user && $user->store_id) {
            $data['store_id'] = $user->store_id;
        }

        // Dynamic Category Creation
        if (empty($data['category_id']) && !empty($data['category_name'])) {
            $category = Category::create([
                'name' => $data['category_name'],
                'slug' => \Illuminate\Support\Str::slug($data['category_name']),
                'store_id' => $data['store_id'] ?? null,
            ]);
            $data['category_id'] = $category->id;
        }

        // Dynamic Brand Creation
        if (empty($data['brand_id']) && !empty($data['brand_name'])) {
            $brand = Brand::create([
                'name' => $data['brand_name'],
                'slug' => \Illuminate\Support\Str::slug($data['brand_name']),
                'store_id' => $data['store_id'] ?? null,
            ]);
            $data['brand_id'] = $brand->id;
        }

        $product = Product::create($data);

        // Auto-create Inventory record if product is created within a store context
        if (!empty($data['store_id'])) {
            Inventory::create([
                'store_id' => $data['store_id'],
                'product_id' => $product->id,
                'quantity' => $data['quantity'] ?? 0,
                'reorder_level' => $data['reorder_level'] ?? 10,
                'reorder_quantity' => $data['reorder_quantity'] ?? 50,
                'cost_price' => $data['cost_price'] ?? 0,
                'selling_price' => $data['selling_price'] ?? 0,
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => $product,
            'categories' => Category::all(),
            'brands' => Brand::all(),
        ]);
    }

    public function update(StoreProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
