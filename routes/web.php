<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AgentController;
use App\Models\ContactMessage;
use App\Models\Review;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
// Главная страница
Route::get('/', function () {
    $properties = \App\Models\Property::with(['images', 'user'])
        ->where('status', 'active')
        ->latest()
        ->take(5)
        ->get();

    $sliderProperties = $properties->map(function($p) {
        return [
            'title' => $p->title,
            'price' => number_format($p->price, 0, '.', ' ') . ' сом',
            'description' => $p->description,
            'image' => $p->mainImage ? asset($p->mainImage->path) : asset('images/header-house.png'),
            'url' => route('properties.show', $p),
            'dealType' => $p->deal_type === 'sale' ? 'Продажа' : 'Аренда',
            'city' => $p->city,
            'icons' => [
                ['src' => asset('images/bed.svg'), 'alt' => 'Комнаты', 'label' => ($p->rooms ?? '—') . ' комн.'],
                ['src' => asset('images/bath.svg'), 'alt' => 'Ванные', 'label' => '3 ванные'],
                ['src' => asset('images/square.svg'), 'alt' => 'Площадь', 'label' => ($p->area ?? '—') . ' м²'],
            ],
        ];
    });

    $counts = [
        'sale_1' => \App\Models\Property::where('deal_type','sale')->where('rooms',1)->count(),
        'sale_2' => \App\Models\Property::where('deal_type','sale')->where('rooms',2)->count(),
        'sale_3' => \App\Models\Property::where('deal_type','sale')->where('rooms',3)->count(),
        'sale_4' => \App\Models\Property::where('deal_type','sale')->where('rooms',4)->count(),
        'sale_total' => \App\Models\Property::where('deal_type','sale')->count(),
        'rent_1' => \App\Models\Property::where('deal_type','rent')->where('rooms',1)->count(),
        'rent_2' => \App\Models\Property::where('deal_type','rent')->where('rooms',2)->count(),
        'rent_3' => \App\Models\Property::where('deal_type','rent')->where('rooms',3)->count(),
        'rent_4' => \App\Models\Property::where('deal_type','rent')->where('rooms',4)->count(),
        'rent_total' => \App\Models\Property::where('deal_type','rent')->count(),
    ];

    return view('welcome', compact('sliderProperties', 'counts'));
})->name('home');

Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
Route::get('/agents/{agent}', [AgentController::class, 'show'])->name('agents.show');
Route::view('/realty-hub', 'for-sale')->name('realty-hub.index');
Route::post('/realty-hub', function (Request $request) {
    $validated = $request->validate([
        'request_type' => ['required', 'in:buy,rent,sell,rent_out'],
        'name' => ['required', 'string', 'max:100'],
        'phone' => ['required', 'string', 'max:30'],
        'city' => ['required', 'string', 'max:100'],
        'property_type' => ['required', 'string', 'max:100'],
        'rooms' => ['nullable', 'integer', 'min:1', 'max:20'],
        'budget' => ['nullable', 'numeric', 'min:0'],
        'comment' => ['nullable', 'string', 'max:2000'],
    ]);

    $typeLabel = match ($validated['request_type']) {
        'buy' => 'Купить',
        'rent' => 'Снять',
        'sell' => 'Продать',
        default => 'Сдать в аренду',
    };
    $message = "Тип заявки: {$typeLabel}\n"
        . "Город: {$validated['city']}\n"
        . "Тип объекта: {$validated['property_type']}\n"
        . "Комнат: " . ($validated['rooms'] ?? 'не указано') . "\n"
        . "Бюджет: " . (isset($validated['budget']) ? number_format((float) $validated['budget'], 0, '.', ' ') . ' сом' : 'не указан') . "\n"
        . "Комментарий: " . ($validated['comment'] ?? 'нет');

    ContactMessage::create([
        'name' => $validated['name'],
        'email' => 'no-email@local.test',
        'phone' => $validated['phone'],
        'message' => $message,
    ]);

    return response()->json(['success' => true]);
})->name('realty-hub.submit');
Route::redirect('/for-sale', '/realty-hub', 301);
Route::view('/services', 'services')->name('services.index');
Route::view('/about', 'about')->name('about.index');
Route::view('/contacts', 'contacts')->name('contacts.index');
Route::post('/contacts', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:100'],
        'email' => ['required', 'email', 'max:150'],
        'phone' => ['required', 'string', 'max:30'],
        'message' => ['required', 'string', 'max:2000'],
    ]);

    ContactMessage::create($validated);

    return response()->json(['success' => true]);
})->name('contacts.submit');
Route::get('/reviews', function () {
    $reviews = Review::query()
        ->where('is_published', true)
        ->latest()
        ->take(30)
        ->get();

    return view('reviews', compact('reviews'));
})->name('reviews.index');
Route::post('/reviews', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:100'],
        'city' => ['nullable', 'string', 'max:100'],
        'rating' => ['required', 'integer', 'min:1', 'max:5'],
        'message' => ['required', 'string', 'max:2000'],
    ]);

    Review::create([
        ...$validated,
        'is_published' => true,
    ]);

    return response()->json(['success' => true]);
})->name('reviews.submit');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:agent'])->group(function () {
    Route::resource('properties', PropertyController::class)->except(['index', 'show']);
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('categories', CategoryController::class);
});

require __DIR__.'/auth.php';
