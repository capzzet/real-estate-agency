@extends('layouts.app')

@section('title', 'Estate-KG — Агентство недвижимости')

@section('content')

    <div class="info-block">
        <h1 class="agency-title">Estate-kg</h1>
        <div class="info-content">
            <div class="info-left">
                <blockquote class="quote">Ваш надёжный эксперт в мире недвижимости</blockquote>
                <form action="{{ route('properties.index') }}" method="get" class="info-search-form">
                    <img src="{{ asset('images/search.svg') }}" alt="Поиск" class="search-icon">
                    <input type="text" name="search" placeholder="Поиск..." class="info-search-input">
                </form>
                <div class="recent-searches">
                    <h3>Недавний поиск:</h3>
                    <ul>
                        @forelse(($recentSearches ?? []) as $searchItem)
                            <li><a href="{{ $searchItem['url'] }}">{{ $searchItem['label'] }}</a></li>
                        @empty
                            <li><a href="{{ route('properties.index', ['search' => 'Центр Бишкек']) }}">Центр Бишкек</a></li>
                            <li><a href="{{ route('properties.index', ['deal_type' => 'sale', 'rooms' => 2]) }}">Продажа · 2 комн.</a></li>
                            <li><a href="{{ route('properties.index', ['deal_type' => 'rent']) }}">Аренда</a></li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="info-right">
                <img src="{{ asset('images/header-house.png') }}" alt="Дом" class="header-house-img">
            </div>
        </div>
    </div>

    <div class="discover-section">
        <div class="left-content">
            <h1>Откройте для себя самые последние объекты недвижимости</h1>
            <p>Ознакомьтесь с нашими последними предложениями потрясающих домов и элитной недвижимости. Найдите недвижимость своей мечты сегодня!</p>
            <a href="{{ route('properties.index') }}">
                <button class="filter-button">Фильтры</button>
            </a>
        </div>
        <div class="right-content">
            <div class="consultation-form">
                <h2>Консультация</h2>
                <form id="consultationForm">
                    @csrf
                    <input type="text" name="name" placeholder="Ваше имя" required>
                    <input type="tel" name="phone" placeholder="Ваш телефон" required>
                    <button type="submit">Получить консультацию</button>
                </form>
                <div id="consultationSuccessMessage" class="success-message"></div>
            </div>
        </div>
    </div>

    <div class="block-container">
        <div class="block">
            <h2>Купить квартиру</h2>
            <ul>
                <li><a href="{{ route('properties.index', ['deal_type'=>'sale','rooms'=>1]) }}">1-комнатные <span class="count">{{ $counts['sale_1'] ?? 0 }}</span></a></li>
                <li><a href="{{ route('properties.index', ['deal_type'=>'sale','rooms'=>2]) }}">2-комнатные <span class="count">{{ $counts['sale_2'] ?? 0 }}</span></a></li>
                <li><a href="{{ route('properties.index', ['deal_type'=>'sale','rooms'=>3]) }}">3-комнатные <span class="count">{{ $counts['sale_3'] ?? 0 }}</span></a></li>
                <li><a href="{{ route('properties.index', ['deal_type'=>'sale','rooms'=>4]) }}">4-комнатные <span class="count">{{ $counts['sale_4'] ?? 0 }}</span></a></li>
                <li><a href="{{ route('properties.index', ['deal_type'=>'sale']) }}">Студии <span class="count">{{ $counts['sale_total'] ?? 0 }}</span></a></li>
            </ul>
        </div>
        <div class="block">
            <h2>Снять квартиру</h2>
            <ul>
                <li><a href="{{ route('properties.index', ['deal_type'=>'rent','rooms'=>1]) }}">1-комнатные <span class="count">{{ $counts['rent_1'] ?? 0 }}</span></a></li>
                <li><a href="{{ route('properties.index', ['deal_type'=>'rent','rooms'=>2]) }}">2-комнатные <span class="count">{{ $counts['rent_2'] ?? 0 }}</span></a></li>
                <li><a href="{{ route('properties.index', ['deal_type'=>'rent','rooms'=>3]) }}">3-комнатные <span class="count">{{ $counts['rent_3'] ?? 0 }}</span></a></li>
                <li><a href="{{ route('properties.index', ['deal_type'=>'rent','rooms'=>4]) }}">4-комнатные <span class="count">{{ $counts['rent_4'] ?? 0 }}</span></a></li>
                <li><a href="{{ route('properties.index', ['deal_type'=>'rent']) }}">Студии <span class="count">{{ $counts['rent_total'] ?? 0 }}</span></a></li>
            </ul>
        </div>
        <div class="block">
            <h2>Собственникам</h2>
            <ul>
                @auth
                    @if(auth()->user()->isAdmin())
                        <li><a href="{{ route('properties.create') }}">Добавить объект</a></li>
                    @else
                        <li><a href="{{ route('realty-hub.index') }}">Оставить заявку на сделку</a></li>
                    @endif
                @else
                    <li><a href="{{ route('login') }}">Продать квартиру</a></li>
                    <li><a href="{{ route('login') }}">Сдать квартиру</a></li>
                @endauth
            </ul>
        </div>
    </div>

    <div id="app">
        <div class="slider-wrapper">
            <div class="slider">
                <div class="slider-container" :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
                    <div v-for="(property, index) in properties" :key="index" class="slide">
                        <div class="property-detail">
                            <div class="property-text">
                                <div class="property-tag">
                                    <span class="tag-deal">@{{ property.dealType }}</span>
                                    <span class="tag-city">@{{ property.city }}</span>
                                </div>
                                <h2 class="property-title">@{{ property.title }}</h2>
                                <span class="property-price">@{{ property.price }}</span>
                                <p class="property-description">@{{ property.description }}</p>
                                <div class="property-icons">
                                    <div v-for="(icon, i) in property.icons" :key="i" class="icon-text">
                                        <img :src="icon.src" :alt="icon.alt" class="property-icon">
                                        <span class="icon-label">@{{ icon.label }}</span>
                                    </div>
                                </div>
                                <a :href="property.url">
                                    <button class="property-button">Подробнее</button>
                                </a>
                            </div>
                            <div class="property-image-container">
                                <img :src="property.image" alt="Изображение дома" class="property-image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slider-dots">
                <span
                    v-for="(property, index) in properties"
                    :key="index"
                    class="slider-dot"
                    :class="{ active: currentSlide === index }"
                    @click="goToSlide(index)">
                </span>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    <script>
        const propertiesData = @json($sliderProperties);
    </script>
    <script src="{{ asset('js/slider.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('consultationForm');
            const successMessage = document.getElementById('consultationSuccessMessage');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    fetch('/consultation', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            name: form.querySelector('[name=name]').value,
                            phone: form.querySelector('[name=phone]').value
                        })
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                successMessage.textContent = 'Форма успешно отправлена!';
                                successMessage.style.display = 'block';
                                form.reset();
                                setTimeout(() => { successMessage.style.display = 'none'; }, 3000);
                            }
                        });
                });
            }
        });
    </script>
@endsection
