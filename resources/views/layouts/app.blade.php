<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Estate-KG')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
</head>
<body>
<div class="container">

    <div class="modal" id="callbackModal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Заказать звонок</h2>
            <form id="callbackForm" action="{{ url('/callback') }}" method="post">
                @csrf
                <div class="form-group">
                    <label>Ваше имя:</label>
                    <input type="text" name="name" placeholder="Имя" required>
                </div>
                <div class="form-group">
                    <label>Ваш телефон:</label>
                    <input type="tel" name="phone" placeholder="Телефон" required>
                </div>
                <button type="submit">Отправить</button>
            </form>
            <div id="successMessage" style="display:none;">Успешно!</div>
        </div>
    </div>

    <header class="header">
        <div class="header-left">
            <a href="{{ url('/') }}"><img src="{{ asset('images/logo1.png') }}" alt="Логотип" class="logo-icon"></a>
            <img src="{{ asset('images/logo2.png') }}" alt="" class="logo-wand-icon">
            <a href="#" class="city-name"><span>Бишкек</span></a>
            <img src="{{ asset('images/location.svg') }}" alt="" class="icon">
        </div>
        <div class="header-right">
            <div class="call">
                <a href="#" id="openModal">
                    <img src="{{ asset('images/call.svg') }}" alt="" class="button-icon">
                    <span>Заказать звонок</span>
                </a>
            </div>
            <div class="phone-numbers">
                <span class="phone-number">+996 123 456 789</span>
                <span class="phone-number">+996 123 456 789</span>
            </div>
        </div>
    </header>

    <nav class="main-nav">
        <ul>
            <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Главная</a></li>
            <li><a href="{{ route('properties.index') }}" class="{{ request()->is('properties*') ? 'active' : '' }}">Каталог</a></li>
            <li><a href="{{ route('realty-hub.index') }}" class="{{ request()->is('for-sale') || request()->is('realty-hub') ? 'active' : '' }}">Все сделки</a></li>
            <li><a href="{{ url('services') }}" class="{{ request()->is('services') ? 'active' : '' }}">Услуги</a></li>
            <li><a href="{{ url('about') }}" class="{{ request()->is('about') ? 'active' : '' }}">О компании</a></li>
            <li><a href="{{ url('contacts') }}" class="{{ request()->is('contacts') ? 'active' : '' }}">Контакты</a></li>
            <li><a href="{{ url('reviews') }}" class="{{ request()->is('reviews') ? 'active' : '' }}">Отзывы</a></li>
        </ul>
    </nav>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="page-content">
        @yield('content')
    </div>

    <div class="ai-assistant" id="aiAssistant">
        <button type="button" class="ai-assistant-toggle" id="aiAssistantToggle" aria-expanded="false" aria-controls="aiAssistantPanel">
            <span class="ai-assistant-toggle-icon"><i class="fas fa-robot"></i></span>
            <span>AI Помощник</span>
        </button>
        <div class="ai-assistant-panel" id="aiAssistantPanel" hidden>
            <div class="ai-assistant-header">
                <h3>Estate AI</h3>
                <div class="ai-assistant-header-actions">
                    <button type="button" class="ai-assistant-reset" id="aiAssistantReset">Новый запрос</button>
                    <button type="button" class="ai-assistant-close" id="aiAssistantClose" aria-label="Закрыть чат">&times;</button>
                </div>
            </div>
            <div class="ai-assistant-messages" id="aiAssistantMessages">
                <div class="ai-message ai-message-bot">Привет! Я помогу с выбором недвижимости, услугами и заявками. Напишите ваш вопрос.</div>
            </div>
            <form class="ai-assistant-form" id="aiAssistantForm">
                <input type="text" id="aiAssistantInput" placeholder="Например: нужна 2-комнатная в центре" autocomplete="off" required>
                <button type="submit">Отправить</button>
            </form>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-column navigation-column">
                <h3>Навигация</h3>
                <ul>
                    <li><a href="{{ url('/') }}">Главная</a></li>
                    <li><a href="{{ route('properties.index') }}">Каталог</a></li>
                    <li><a href="{{ url('contacts') }}">Контакты</a></li>
                    <li><a href="#">Блог</a></li>
                    <li><a href="{{ url('about') }}">О компании</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Свяжитесь с нами</h3>
                <ul>
                    <li><a href="tel:+996123456789"><i class="fas fa-phone"></i> +996 (123) 456-78-90</a></li>
                    <li><a href="mailto:eltimyr@gmail.com"><i class="fas fa-envelope"></i> eltimyr@gmail.com</a></li>
                    <li><a href="#"><i class="fas fa-map-marker-alt"></i> г. Бишкек, ул. Уличная, д. 1</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Социальные сети</h3>
                <ul class="social-links">
                    <li><a href="#"><i class="fab fa-facebook"></i></a></li>
                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                    <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                    <li><a href="#"><i class="fab fa-tiktok"></i></a></li>
                    <li><a href="#"><i class="fab fa-youtube"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 ESTATE-KG. Все права защищены.</p>
        </div>
    </footer>

</div>

<script src="{{ asset('js/scripts.js') }}"></script>
@yield('scripts')
</body>
</html>
