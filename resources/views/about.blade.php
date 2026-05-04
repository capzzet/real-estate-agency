@extends('layouts.app')

@section('title', 'О компании — Estate-KG')

@section('content')
    <div class="about-unique-page">
        <section class="about-unique-hero">
            <p class="about-unique-kicker">Estate-KG</p>
            <h1>О компании</h1>
            <p>Мы строим сервис недвижимости нового уровня: прозрачный, человечный и технологичный.</p>
            <div class="about-unique-tags">
                <span>7+ лет на рынке</span>
                <span>1200+ сделок</span>
                <span>Поддержка 24/7</span>
            </div>
        </section>

        <section class="about-unique-grid">
            <article class="about-unique-card">
                <h3>Что для нас важно</h3>
                <p>Мы не продаем просто квадратные метры — мы подбираем решение под вашу жизнь, цели и бюджет.</p>
            </article>
            <article class="about-unique-card">
                <h3>Как мы работаем</h3>
                <p>Каждый этап фиксируется: анализ рынка, подбор, показы, проверка документов, безопасное закрытие сделки.</p>
            </article>
            <article class="about-unique-card about-unique-accent">
                <h3>Наша гарантия</h3>
                <p>Юридическая проверка документов и полное сопровождение до передачи ключей.</p>
            </article>
        </section>

        <section class="about-unique-timeline">
            <h2>Путь клиента с нами</h2>
            <div class="about-steps">
                <div class="about-step">
                    <span>01</span>
                    <div>
                        <h4>Консультация</h4>
                        <p>Уточняем задачу, бюджет, сроки и формируем стратегию.</p>
                    </div>
                </div>
                <div class="about-step">
                    <span>02</span>
                    <div>
                        <h4>Подбор и показы</h4>
                        <p>Выбираем релевантные объекты и организуем показы в удобное время.</p>
                    </div>
                </div>
                <div class="about-step">
                    <span>03</span>
                    <div>
                        <h4>Проверка сделки</h4>
                        <p>Проверяем юридическую чистоту и готовим пакет документов.</p>
                    </div>
                </div>
                <div class="about-step">
                    <span>04</span>
                    <div>
                        <h4>Закрытие и поддержка</h4>
                        <p>Сопровождаем до финала и остаемся на связи после сделки.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-unique-cta">
            <h2>Готовы обсудить вашу задачу?</h2>
            <p>Покупка, продажа или аренда — подберем лучший вариант вместе.</p>
            <a href="{{ route('contacts.index') }}" class="about-unique-btn">Оставить заявку</a>
        </section>
    </div>
@endsection