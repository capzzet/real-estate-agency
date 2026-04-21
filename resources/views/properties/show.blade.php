@extends('layouts.app')

@section('title', $property->title . ' — Estate-KG')

@section('content')
    <div class="property-show-page">
        <div class="property-show-head">
            <div>
                <h1>{{ $property->title }}</h1>
                <p class="property-show-location">
                    <img src="{{ asset('images/location.svg') }}" alt="">
                    {{ $property->city }}, {{ $property->address }}
                </p>
            </div>
            <div class="property-show-price-wrap">
                <span class="property-show-deal">{{ $property->deal_type === 'sale' ? 'Продажа' : 'Аренда' }}</span>
                <span class="property-show-price">{{ number_format($property->price, 0, '.', ' ') }} сом</span>
            </div>
        </div>

        <div class="property-show-layout">
            <div class="property-show-main">
                @php
                    $mainImage = $property->images->firstWhere('is_main', true) ?? $property->images->first();
                @endphp

                <div class="property-show-gallery">
                    <div class="property-show-main-image">
                        <img
                            id="mainPropertyImage"
                            src="{{ $mainImage ? asset($mainImage->path) : asset('images/header-house.png') }}"
                            alt="{{ $property->title }}"
                        >
                    </div>

                    @if($property->images->count() > 1)
                        <div class="property-show-thumbs">
                            @foreach($property->images as $image)
                                <button
                                    class="property-thumb {{ $mainImage && $mainImage->id === $image->id ? 'active' : '' }}"
                                    type="button"
                                    data-src="{{ asset($image->path) }}"
                                >
                                    <img src="{{ asset($image->path) }}" alt="Фото объекта">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="property-show-section">
                    <h2>Описание</h2>
                    <p>{{ $property->description }}</p>
                </div>
            </div>

            <aside class="property-show-sidebar">
                <div class="property-show-section">
                    <h3>Характеристики</h3>
                    <ul class="property-facts">
                        <li><span>Категория</span><strong>{{ $property->category->name ?? '—' }}</strong></li>
                        <li><span>Комнат</span><strong>{{ $property->rooms ?? '—' }}</strong></li>
                        <li><span>Площадь</span><strong>{{ $property->area ? $property->area . ' м²' : '—' }}</strong></li>
                        <li><span>Статус</span><strong>{{ $property->status === 'active' ? 'Активно' : 'Не активно' }}</strong></li>
                        <li><span>Добавлено</span><strong>{{ $property->created_at->format('d.m.Y') }}</strong></li>
                    </ul>
                </div>

                <div class="property-show-section agent-contact">
                    <h3>Контакт агента</h3>
                    <div class="agent-contact-row">
                        <div class="agent-avatar">{{ strtoupper(mb_substr($property->user->name, 0, 1)) }}</div>
                        <div>
                            <p class="agent-name">{{ $property->user->name }}</p>
                            <p class="agent-phone">{{ $property->user->phone ?: 'Телефон не указан' }}</p>
                        </div>
                    </div>
                    @if($property->user->phone)
                        <a href="tel:{{ preg_replace('/\s+/', '', $property->user->phone) }}" class="agent-contact-btn">Позвонить</a>
                    @endif
                </div>
            </aside>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mainImage = document.getElementById('mainPropertyImage');
            const thumbs = document.querySelectorAll('.property-thumb');
            if (!mainImage || !thumbs.length) {
                return;
            }

            thumbs.forEach((thumb) => {
                thumb.addEventListener('click', function () {
                    mainImage.src = this.dataset.src;
                    thumbs.forEach((item) => item.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>
@endsection
