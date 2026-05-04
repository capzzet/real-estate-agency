@extends('layouts.app')

@section('title', 'Каталог — Estate-KG')

@section('content')

    <div class="catalog-page">
        <div class="catalog-hero">
            <div>
                <h1>Каталог недвижимости</h1>
                <p>Умные фильтры, свежие предложения и быстрый подбор под ваш запрос.</p>
            </div>
            <div class="catalog-hero-stats">
                <span>{{ $properties->total() }} объектов</span>
                <span>{{ request()->filled('deal_type') ? (request('deal_type') === 'sale' ? 'Продажа' : 'Аренда') : 'Все сделки' }}</span>
            </div>
        </div>

        <div class="catalog-filters catalog-filters-modern">
            <form method="GET" action="{{ route('properties.index') }}" class="filters-form">
                <div class="filters-row">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск по названию, району, адресу" class="filter-input filter-search">

                    <select name="sort" class="filter-select">
                        <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Сначала новые</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Цена: по возрастанию</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Цена: по убыванию</option>
                        <option value="area_desc" {{ request('sort') === 'area_desc' ? 'selected' : '' }}>Площадь: больше сначала</option>
                    </select>
                </div>

                <div class="filters-row">
                    <select name="city" class="filter-select">
                        <option value="">Любой город</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>

                    <select name="category_id" class="filter-select">
                        <option value="">Все категории</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="deal_type" class="filter-select">
                        <option value="">Продажа и аренда</option>
                        <option value="sale" {{ request('deal_type') == 'sale' ? 'selected' : '' }}>Продажа</option>
                        <option value="rent" {{ request('deal_type') == 'rent' ? 'selected' : '' }}>Аренда</option>
                    </select>

                    <select name="rooms" class="filter-select">
                        <option value="">Комнаты (любое)</option>
                        <option value="1" {{ request('rooms') == '1' ? 'selected' : '' }}>1 комната</option>
                        <option value="2" {{ request('rooms') == '2' ? 'selected' : '' }}>2 комнаты</option>
                        <option value="3" {{ request('rooms') == '3' ? 'selected' : '' }}>3 комнаты</option>
                        <option value="4" {{ request('rooms') == '4' ? 'selected' : '' }}>4+ комнаты</option>
                    </select>

                    <div class="filter-buttons">
                        <button type="submit" class="filter-button">Применить</button>
                        <a href="{{ route('properties.index') }}" class="filter-reset">Сбросить</a>
                        <button type="button" class="filter-advanced-toggle" id="advancedToggle">Ещё фильтры ▾</button>
                    </div>
                </div>

                <div class="filters-advanced" id="advancedFilters">
                    <div class="filters-row">
                        <input
                            type="number"
                            name="price_min"
                            value="{{ request('price_min') }}"
                            placeholder="Цена от ({{ number_format($priceBounds->min_price ?? 0, 0, '.', ' ') }})"
                            class="filter-input"
                        >
                        <input
                            type="number"
                            name="price_max"
                            value="{{ request('price_max') }}"
                            placeholder="Цена до ({{ number_format($priceBounds->max_price ?? 0, 0, '.', ' ') }})"
                            class="filter-input"
                        >
                        <input type="number" name="area_min" value="{{ request('area_min') }}" placeholder="Площадь от (м²)" class="filter-input">
                        <input type="number" name="area_max" value="{{ request('area_max') }}" placeholder="Площадь до (м²)" class="filter-input">
                        <input type="number" name="rooms_min" value="{{ request('rooms_min') }}" placeholder="Комнат от" class="filter-input">
                        <input type="number" name="rooms_max" value="{{ request('rooms_max') }}" placeholder="Комнат до" class="filter-input">
                    </div>
                    <div class="filters-row">
                        <input type="text" name="agent_name" value="{{ request('agent_name') }}" placeholder="Имя агента" class="filter-input">
                        <select name="posted_within" class="filter-select">
                            <option value="">Дата публикации</option>
                            <option value="1" {{ request('posted_within') == '1' ? 'selected' : '' }}>За сегодня</option>
                            <option value="3" {{ request('posted_within') == '3' ? 'selected' : '' }}>За 3 дня</option>
                            <option value="7" {{ request('posted_within') == '7' ? 'selected' : '' }}>За неделю</option>
                            <option value="30" {{ request('posted_within') == '30' ? 'selected' : '' }}>За месяц</option>
                            <option value="90" {{ request('posted_within') == '90' ? 'selected' : '' }}>За 3 месяца</option>
                        </select>
                        <select name="has_photo" class="filter-select">
                            <option value="">Фото</option>
                            <option value="1" {{ request('has_photo') == '1' ? 'selected' : '' }}>Только с фото</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div class="catalog-grid">
            @forelse($properties as $property)
                <div class="property-card">
                    <div class="card-image">
                        @if($property->mainImage)
                            <img src="{{ asset($property->mainImage->path) }}" alt="{{ $property->title }}">
                        @else
                            <div class="card-no-image">Нет фото</div>
                        @endif
                        <div class="card-badges">
                            <span class="badge-deal">{{ $property->deal_type === 'sale' ? 'Продажа' : 'Аренда' }}</span>
                            <span class="badge-category">{{ $property->category->name }}</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <h3 class="card-title">{{ $property->title }}</h3>
                        <p class="card-description">{{ \Illuminate\Support\Str::limit($property->description, 96) }}</p>

                        <p class="card-address">
                            <img src="{{ asset('images/location.svg') }}" alt="" class="card-location-icon">
                            {{ $property->city }}, {{ $property->address }}
                        </p>

                        <div class="card-specs">
                            @if($property->rooms)
                                <span class="card-spec">
                                    <img src="{{ asset('images/bed.svg') }}" alt="" class="spec-icon">
                                    {{ $property->rooms }} комн.
                                </span>
                            @endif
                            @if($property->area)
                                <span class="card-spec">
                                    <img src="{{ asset('images/square.svg') }}" alt="" class="spec-icon">
                                    {{ $property->area }} м²
                                </span>
                            @endif
                            @if($property->area && $property->price)
                                <span class="card-spec">
                                    {{ number_format($property->price / max($property->area, 1), 0, '.', ' ') }} сом/м²
                                </span>
                            @endif
                            <span class="card-spec">
                                {{ $property->images_count }} фото
                            </span>
                        </div>

                        <div class="card-footer">
                            <span class="card-price">{{ number_format($property->price, 0, '.', ' ') }} сом</span>
                            <a href="{{ route('properties.show', $property) }}" class="card-button">Подробнее</a>
                        </div>

                        <div class="card-agent">
                            <div class="agent-avatar">{{ substr($property->user->name, 0, 1) }}</div>
                            <div>
                                <p class="agent-name">{{ $property->user->name }}</p>
                                <p class="agent-phone">{{ $property->user->phone ?: 'Контакты в карточке объекта' }}</p>
                            </div>
                            <span class="card-date">{{ $property->created_at->format('d.m.Y') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="catalog-empty">
                    <p>Объекты не найдены</p>
                    <a href="{{ route('properties.index') }}">Сбросить фильтры</a>
                </div>
            @endforelse
        </div>

        <div class="catalog-pagination">
            {{ $properties->withQueryString()->links() }}
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('advancedToggle');
            const advanced = document.getElementById('advancedFilters');

            const advancedInputs = advanced.querySelectorAll('input, select');
            let hasValues = false;
            advancedInputs.forEach(input => { if (input.value) hasValues = true; });
            if (hasValues) {
                advanced.classList.add('open');
                toggle.classList.add('open');
                toggle.textContent = 'Скрыть ▴';
            }

            toggle.addEventListener('click', function () {
                advanced.classList.toggle('open');
                toggle.classList.toggle('open');
                toggle.textContent = advanced.classList.contains('open') ? 'Скрыть ▴' : 'Ещё фильтры ▾';
            });
        });
    </script>
@endsection