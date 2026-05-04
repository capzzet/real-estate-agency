<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AgentController;
use App\Models\ContactMessage;
use App\Models\Property;
use App\Models\Review;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
Route::get('/', function () {
    $properties = \App\Models\Property::with(['mainImage', 'user'])
        ->where('status', 'active')
        ->orderByDesc('created_at')
        ->orderByDesc('id')
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

    $recentSearches = session('recent_searches', []);

    return view('welcome', compact('sliderProperties', 'counts', 'recentSearches'));
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
        'source' => 'realty_hub',
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

    ContactMessage::create([
        ...$validated,
        'source' => 'contacts',
    ]);

    return response()->json(['success' => true]);
})->name('contacts.submit');
Route::post('/consultation', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:100'],
        'phone' => ['required', 'string', 'max:30'],
    ]);

    ContactMessage::create([
        'name' => $validated['name'],
        'email' => 'consultation@local.test',
        'phone' => $validated['phone'],
        'message' => 'Заявка: Консультация с главной страницы',
        'source' => 'consultation',
    ]);

    return response()->json(['success' => true]);
})->name('consultation.submit');
Route::post('/callback', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:100'],
        'phone' => ['required', 'string', 'max:30'],
    ]);

    ContactMessage::create([
        'name' => $validated['name'],
        'email' => 'callback@local.test',
        'phone' => $validated['phone'],
        'message' => 'Заявка: Заказать звонок',
        'source' => 'callback',
    ]);

    return response()->json(['success' => true, 'message' => 'Успешно!']);
})->name('callback.submit');
Route::post('/ai-assistant/reset', function () {
    session()->forget(['ai_chat_history', 'ai_owner_lead_id', 'ai_chat_meta', 'ai_flow_mode', 'ai_owner_action']);

    return response()->json([
        'success' => true,
        'message' => 'Диалог очищен. Можем начать новый запрос.',
    ]);
})->name('ai-assistant.reset');
Route::post('/ai-assistant/chat', function (Request $request) {
    $validated = $request->validate([
        'message' => ['required', 'string', 'max:2000'],
    ]);
    $chatId = trim((string) $request->input('chat_id', ''));

    $apiKey = env('GROQ_API_KEY', env('XAI_API_KEY'));
    if (!$apiKey) {
        return response()->json([
            'success' => false,
            'message' => 'Не настроен GROQ_API_KEY в .env',
        ], 500);
    }

    $messageText = trim((string) $validated['message']);
    $messageLower = mb_strtolower($messageText);

    $phone = null;
    if (preg_match('/(\+?\d[\d\-\s\(\)]{7,}\d)/u', $messageText, $phoneMatch)) {
        $phone = trim($phoneMatch[1]);
    }

    $rooms = null;
    if (preg_match('/\b(\d)\s*[- ]?(ком|комн|комнат)/u', $messageLower, $roomsMatch)) {
        $rooms = (int) $roomsMatch[1];
    } elseif (preg_match('/\bдвухкомн|двушка/u', $messageLower)) {
        $rooms = 2;
    } elseif (preg_match('/\bтрехкомн|трешка/u', $messageLower)) {
        $rooms = 3;
    } elseif (preg_match('/\bодн(о|а)комн/u', $messageLower)) {
        $rooms = 1;
    }

    $area = null;
    if (preg_match('/(\d{2,4}(?:[.,]\d+)?)\s*(м2|м²|кв\.?\s?м|квадрат)/u', $messageLower, $areaMatch)) {
        $area = str_replace(',', '.', $areaMatch[1]);
    } elseif (preg_match('/\bсто\s*квадрат/u', $messageLower)) {
        $area = '100';
    } elseif (preg_match('/\bдвести\s*квадрат/u', $messageLower)) {
        $area = '200';
    } elseif (preg_match('/\bтриста\s*квадрат/u', $messageLower)) {
        $area = '300';
    }

    $price = null;
    if (preg_match('/(?:за|цена|бюджет|стоимост[ьи])\s*[:\-]?\s*(\d[\d\s]{3,})/u', $messageLower, $priceMatch)) {
        $price = preg_replace('/\s+/', '', $priceMatch[1]);
    } elseif (preg_match('/\bполтораста\s*тыс/u', $messageLower)) {
        $price = '150000';
    } elseif (preg_match('/\bсто\s*тыс/u', $messageLower)) {
        $price = '100000';
    } elseif (preg_match('/\bдвести\s*тыс/u', $messageLower)) {
        $price = '200000';
    }

    $action = null;
    if (preg_match('/\b(продам|продать|продаю|продажа)\b/u', $messageLower)) {
        $action = 'sell';
    } elseif (preg_match('/\b(сдам|сдать|сдаю|арендодатель)\b/u', $messageLower)) {
        $action = 'rent_out';
    } elseif (preg_match('/\b(купить|куплю|покупка)\b/u', $messageLower)) {
        $action = 'buy';
    } elseif (preg_match('/\b(снять|сниму|аренда|арендовать)\b/u', $messageLower)) {
        $action = 'rent';
    }

    $propertyType = null;
    if (preg_match('/\b(дом|квартира|участок|коммерц|офис|таунхаус|коттедж)\b/u', $messageLower, $propertyTypeMatch)) {
        $propertyType = $propertyTypeMatch[1];
    }

    $city = null;
    if (preg_match('/\bбишкек(а|е|у|ом)?\b/u', $messageLower) || preg_match('/\bбишке?к(а|е|у|ом)?\b/u', $messageLower)) {
        $city = 'Бишкек';
    } elseif (preg_match('/\bош\b/u', $messageLower)) {
        $city = 'Ош';
    }

    $district = null;
    if (preg_match('/(?:район|мкр|микрорайон)\s+([a-zа-я0-9\-\s]+)/ui', $messageText, $districtMatch)) {
        $district = trim($districtMatch[1]);
        $district = preg_replace('/[,.!?].*$/u', '', $district);
    } elseif (preg_match('/\bцентр\b/u', $messageLower)) {
        $district = 'Центр';
    } elseif (preg_match('/\bполитех\b/u', $messageLower)) {
        $district = 'Политех';
    }

    $floor = null;
    if (preg_match('/\b(\d{1,2})\s*этаж\b/u', $messageLower, $floorMatch)) {
        $floor = (int) $floorMatch[1];
    }

    $hasParking = preg_match('/\b(паркинг|гараж|подземн(ый|ого)\s*паркинг)\b/u', $messageLower) === 1;
    $hasRenovation = preg_match('/\b(ремонт|евро\s*ремонт|свежий\s*ремонт|дизайнерский)\b/u', $messageLower) === 1;
    $isGreeting = preg_match('/^\s*(привет|здравствуйте|добрый\s+день|салам|hello|hi)\s*!?\s*$/ui', $messageText) === 1;
    $hasPropertyClues = collect([
        $rooms,
        $area,
        $price,
        $city,
        $propertyType,
        $district,
        $floor,
        $phone,
        $hasParking ? 'parking' : null,
        $hasRenovation ? 'renovation' : null,
    ])->filter()->isNotEmpty();

    $detailScore = collect([$rooms, $area, $price, $city, $propertyType])->filter()->count();
    $hasLeadIntent = $action !== null;
    $leadCreated = false;
    $leadUpdated = false;
    $sessionFlow = (string) session('ai_flow_mode', '');
    $explicitOwnerAction = in_array($action, ['sell', 'rent_out'], true);
    $explicitBuyerAction = in_array($action, ['buy', 'rent'], true);
    $isOwnerFlow = $explicitOwnerAction || ($action === null && $sessionFlow === 'owner' && $hasPropertyClues && !$isGreeting);
    $isBuyerFlow = !$isOwnerFlow;

    if ($explicitOwnerAction) {
        session()->forget('ai_owner_lead_id');
        session(['ai_flow_mode' => 'owner']);
    } elseif ($explicitBuyerAction) {
        session()->forget(['ai_owner_lead_id', 'ai_owner_action']);
        session(['ai_flow_mode' => 'buyer']);
    }
    $actionLabel = null;

    if ($isOwnerFlow) {
        if ($action === null) {
            $action = session('ai_owner_action', 'sell');
        } else {
            session(['ai_owner_action' => $action]);
        }
        $actionLabel = match ($action) {
            'sell' => 'Продажа',
            'rent_out' => 'Сдача в аренду',
            default => 'Заявка',
        };

        $leadMessage = "AI-заявка ({$actionLabel})\n"
            . "Тип объекта: " . ($propertyType ?: 'не указан') . "\n"
            . "Город: " . ($city ?: 'не указан') . "\n"
            . "Район: " . ($district ?: 'не указан') . "\n"
            . "Комнаты: " . ($rooms ?: 'не указаны') . "\n"
            . "Площадь: " . ($area ? "{$area} м²" : 'не указана') . "\n"
            . "Этаж: " . ($floor ?: 'не указан') . "\n"
            . "Ремонт: " . ($hasRenovation ? 'есть' : 'не указано') . "\n"
            . "Паркинг/гараж: " . ($hasParking ? 'есть' : 'не указано') . "\n"
            . "Цена/бюджет: " . ($price ? number_format((float) $price, 0, '.', ' ') . ' сом' : 'не указаны') . "\n"
            . "Оригинальный текст:\n{$messageText}";

        $leadId = session('ai_owner_lead_id');
        $leadRecord = $leadId ? ContactMessage::find($leadId) : null;

        if ($leadRecord && $leadRecord->source === 'ai_chat') {
            $leadRecord->update([
                'phone' => $phone ?: $leadRecord->phone,
                'message' => $leadMessage,
            ]);
            $leadUpdated = true;
            Log::info('AI owner lead updated', ['lead_id' => $leadRecord->id]);
        } else {
            $leadRecord = ContactMessage::create([
                'name' => 'Клиент из AI-чата',
                'email' => 'ai-chat@local.test',
                'phone' => $phone ?: 'не указан',
                'message' => $leadMessage,
                'source' => 'ai_chat',
            ]);
            session(['ai_owner_lead_id' => $leadRecord->id]);
            $leadCreated = true;
            Log::info('AI owner lead created', ['lead_id' => $leadRecord->id]);
        }
    }

    $catalogContext = '';
    if ($isBuyerFlow) {
        $catalogQuery = Property::query()
            ->where('status', 'active')
            ->select(['id', 'title', 'city', 'address', 'rooms', 'area', 'price', 'deal_type', 'created_at']);

        if ($rooms !== null) {
            $catalogQuery->where('rooms', $rooms);
        }

        if (str_contains($messageLower, 'бишкек')) {
            $catalogQuery->where('city', 'like', '%Бишкек%');
        }

        if (str_contains($messageLower, 'центр')) {
            $catalogQuery->where(function ($query) {
                $query->where('address', 'like', '%центр%')
                    ->orWhere('title', 'like', '%центр%')
                    ->orWhere('description', 'like', '%центр%');
            });
        }

        if ($action === 'rent') {
            $catalogQuery->where('deal_type', 'rent');
        } elseif ($action === 'buy') {
            $catalogQuery->where('deal_type', 'sale');
        }

        $catalogItems = $catalogQuery
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $catalogSummary = $catalogItems->map(function (Property $property) {
            $price = number_format((float) $property->price, 0, '.', ' ') . ' сом';
            $dealTypeLabel = $property->deal_type === 'rent' ? 'Аренда' : 'Продажа';
            $roomsLabel = $property->rooms ? "{$property->rooms} комн." : 'Комнаты не указаны';
            $areaLabel = $property->area ? rtrim(rtrim((string) $property->area, '0'), '.') . ' м²' : 'Площадь не указана';
            $url = route('properties.show', $property);

            return "- {$property->title} | {$dealTypeLabel} | {$property->city} | {$property->address} | {$roomsLabel} | {$areaLabel} | {$price} | {$url}";
        })->implode("\n");

        $catalogContext = $catalogSummary !== ''
            ? "Актуальные подходящие объекты из каталога:\n{$catalogSummary}"
            : 'В каталоге нет точных совпадений по этому запросу. Предложи ближайший вариант (по сделке/городу/комнатам) и задай 1-2 уточняющих вопроса.';
    }

    $systemPrompt = $isOwnerFlow
        ? "Ты живой и вежливый AI-менеджер агентства недвижимости Estate-KG для собственников. "
            . "Пользователь хочет продать или сдать объект. Отвечай ТОЛЬКО на русском языке, простыми человеческими фразами, без иностранных слов и символов. "
            . "НЕ предлагай объекты из каталога и НЕ сравнивай с похожими квартирами. "
            . "Твоя задача: подтвердить, что заявка принята в работу, коротко повторить извлеченные параметры и запросить только недостающие важные данные (район, телефон, удобное время связи)."
        : "Ты AI-консультант агентства недвижимости Estate-KG. Отвечай кратко, вежливо и по делу на русском языке. "
            . "Используй данные каталога ниже как главный источник правды. Если есть релевантные объекты, перечисли 2-4 лучших варианта с ценой и ссылкой. "
            . "Если точных совпадений нет, честно сообщи и предложи альтернативы.\n\n"
            . $catalogContext;

    $historyMeta = session('ai_chat_meta', []);
    $history = session('ai_chat_history', []);
    $nowTs = now()->timestamp;
    $ttlSeconds = 1800;
    $prevChatId = trim((string) ($historyMeta['chat_id'] ?? ''));
    $prevUpdatedAt = (int) ($historyMeta['updated_at'] ?? 0);
    $expired = $prevUpdatedAt > 0 && ($nowTs - $prevUpdatedAt) > $ttlSeconds;
    // Если chat_id не передается, считаем это тем же диалогом в рамках текущей сессии.
    $chatChanged = $chatId !== '' && $prevChatId !== '' && $chatId !== $prevChatId;
    if ($expired || $chatChanged) {
        $history = [];
        session()->forget(['ai_owner_lead_id', 'ai_flow_mode', 'ai_owner_action']);
    }
    $history[] = ['role' => 'user', 'content' => $messageText];
    $history = array_slice($history, -12);
    $model = env('GROQ_MODEL', env('XAI_MODEL', 'llama-3.3-70b-versatile'));
    $baseUrl = rtrim(env('GROQ_BASE_URL', env('XAI_BASE_URL', 'https://api.groq.com/openai/v1')), '/');
    $caBundle = env('GROQ_CA_BUNDLE', env('XAI_CA_BUNDLE'));
    $sslVerify = env('GROQ_SSL_VERIFY', env('XAI_SSL_VERIFY', true));
    $verifyOption = $caBundle ?: filter_var($sslVerify, FILTER_VALIDATE_BOOL);

    $finalMessage = '';
    if ($isOwnerFlow) {
        $finalMessage = "Спасибо, заявку приняли.";
    } else {
        try {
            $response = Http::withToken($apiKey)
                ->withOptions([
                    'verify' => $verifyOption,
                ])
                ->acceptJson()
                ->timeout(25)
                ->post($baseUrl . '/chat/completions', [
                    'model' => $model,
                    'messages' => array_merge(
                        [['role' => 'system', 'content' => $systemPrompt]],
                        $history
                    ),
                    'temperature' => 0.6,
                    'max_tokens' => 350,
                ]);
        } catch (ConnectionException $e) {
            Log::error('AI provider connection error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка SSL/подключения к AI API. Для Windows укажите GROQ_CA_BUNDLE или временно GROQ_SSL_VERIFY=false в .env.',
                'details' => $e->getMessage(),
            ], 502);
        }

        if (!$response->successful()) {
            Log::error('AI API non-success response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'base_url' => $baseUrl,
                'model' => $model,
            ]);

            $debug = (bool) config('app.debug');
            $errorMessage = 'Сервис AI временно недоступен. Попробуйте еще раз.';
            if ($debug) {
                $errorMessage = 'AI API error ' . $response->status() . ': ' . $response->body();
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'details' => $response->json(),
            ], 502);
        }

        $answer = data_get($response->json(), 'choices.0.message.content');
        if (!$answer) {
            return response()->json([
                'success' => false,
                'message' => 'AI вернул пустой ответ. Попробуйте переформулировать вопрос.',
            ], 502);
        }

        $finalMessage = trim($answer);
    }
    if ($isOwnerFlow) {
        $missing = [];
        if (!$city && !$district) {
            $missing[] = 'город или район';
        }
        if (!$phone) {
            $missing[] = 'контактный телефон';
        }
        if (!$price) {
            $missing[] = 'ожидаемая цена';
        }

        if (count($missing)) {
            $finalMessage = "Спасибо, заявку принял. Уточните, пожалуйста: " . implode(', ', $missing) . ".";
        } else {
            $finalMessage = "Отлично, все данные получил. В ближайшее время с вами свяжется менеджер.";
        }
    }

    $history[] = ['role' => 'assistant', 'content' => $finalMessage];
    session([
        'ai_chat_history' => array_slice($history, -12),
        'ai_chat_meta' => [
            'chat_id' => $chatId !== '' ? $chatId : $prevChatId,
            'updated_at' => $nowTs,
        ],
    ]);

    return response()->json([
        'success' => true,
        'message' => $finalMessage
            . (!$isOwnerFlow && $leadCreated ? "\n\nЗаявка принята. Менеджер свяжется с вами." : '')
            . (!$isOwnerFlow && $leadUpdated ? "\n\nДанные заявки обновлены." : ''),
        'lead_created' => $leadCreated,
        'lead_updated' => $leadUpdated,
        'lead_id' => session('ai_owner_lead_id'),
        'owner_flow' => $isOwnerFlow,
        'lead_ready' => $isOwnerFlow && ($detailScore >= 2 || $phone !== null),
    ]);
})->name('ai-assistant.chat');
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

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('properties', PropertyController::class)->except(['index', 'show']);
    Route::resource('categories', CategoryController::class);
});

require __DIR__.'/auth.php';
