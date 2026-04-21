<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Category;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = Property::query()->where('status', 'active');
        $query = Property::with(['category', 'mainImage', 'user'])
            ->where('status', 'active');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('deal_type')) {
            $query->where('deal_type', $request->deal_type);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->filled('rooms')) {
            if ((int) $request->rooms >= 4) {
                $query->where('rooms', '>=', 4);
            } else {
                $query->where('rooms', (int) $request->rooms);
            }
        }

        if ($request->filled('area_min')) {
            $query->where('area', '>=', $request->area_min);
        }

        if ($request->filled('area_max')) {
            $query->where('area', '<=', $request->area_max);
        }

        $sort = $request->get('sort', 'newest');
        if ($sort === 'price_asc') {
            $query->orderBy('price');
        } elseif ($sort === 'price_desc') {
            $query->orderByDesc('price');
        } elseif ($sort === 'area_desc') {
            $query->orderByDesc('area');
        } else {
            $query->latest();
        }

        $properties = $query->paginate(12);
        $categories = Category::all();
        $cities = (clone $baseQuery)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');
        $priceBounds = (clone $baseQuery)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return view('properties.index', compact('properties', 'categories', 'cities', 'priceBounds'));
    }

    public function show(Property $property)
    {
        $property->load(['category', 'images', 'user']);
        return view('properties.show', compact('property'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('properties.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'address'     => 'required|string',
            'city'        => 'required|string',
            'deal_type'   => 'required|in:sale,rent',
            'category_id' => 'required|exists:categories,id',
            'rooms'       => 'nullable|integer|min:1',
            'area'        => 'nullable|numeric|min:0',
        ]);

        $property = Property::create([
            ...$request->only([
                'title', 'description', 'price', 'address',
                'city', 'rooms', 'area', 'deal_type', 'category_id'
            ]),
            'user_id' => auth()->id(),
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties', 'public');
                $property->images()->create([
                    'path'    => $path,
                    'is_main' => $index === 0, // первое фото — главное
                ]);
            }
        }

        return redirect()->route('properties.show', $property)
            ->with('success', 'Объект успешно добавлен');
    }

    public function edit(Property $property)
    {
        $categories = Category::all();
        return view('properties.edit', compact('property', 'categories'));
    }

    public function update(Request $request, Property $property)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'address'     => 'required|string',
            'city'        => 'required|string',
            'deal_type'   => 'required|in:sale,rent',
            'category_id' => 'required|exists:categories,id',
            'rooms'       => 'nullable|integer|min:1',
            'area'        => 'nullable|numeric|min:0',
        ]);

        $property->update($request->only([
            'title', 'description', 'price', 'address',
            'city', 'rooms', 'area', 'deal_type', 'category_id', 'status'
        ]));

        return redirect()->route('properties.show', $property)
            ->with('success', 'Объект успешно обновлён');
    }

    public function destroy(Property $property)
    {
        $property->delete();
        return redirect()->route('properties.index')
            ->with('success', 'Объект удалён');
    }
}
