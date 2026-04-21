@extends('layouts.app')

@section('title', 'Услуги — Estate-KG')

@section('content')
    <div class="info-page info-page-services">
        <div class="info-hero info-hero-rich">
            <div>
                <h1>Услуги Estate-KG</h1>
                <p>Полный цикл на рынке недвижимости: от аналитики цены до передачи ключей и поддержки после сделки.</p>
            </div>
            <div class="info-hero-pills">
                <span><i class="fas fa-bolt"></i> Быстрый старт</span>
                <span><i class="fas fa-shield-alt"></i> Юр. безопасность</span>
                <span><i class="fas fa-chart-line"></i> Рыночная аналитика</span>
            </div>
        </div>

        <div class="service-highlight">
            <div class="service-highlight-card">
                <p class="service-highlight-label">Фокус месяца</p>
                <h2>Продажа под ключ за 21 день</h2>
                <p>Профессиональная съемка, сильная упаковка объявления и стратегия показов, чтобы получить лучшую цену.</p>
            </div>
            <div class="service-kpis">
                <div><strong>1200+</strong><span>закрытых сделок</span></div>
                <div><strong>95%</strong><span>довольных клиентов</span></div>
                <div><strong>7 лет</strong><span>опыта команды</span></div>
            </div>
        </div>

        <div class="services-grid services-grid-rich">
            <article class="info-card service-card">
                <span class="service-icon"><i class="fas fa-home"></i></span>
                <h3>Покупка недвижимости</h3>
                <p>Подбираем варианты под ваш бюджет, проверяем документы и сопровождаем сделку до регистрации.</p>
            </article>
            <article class="info-card service-card">
                <span class="service-icon"><i class="fas fa-tag"></i></span>
                <h3>Продажа объекта</h3>
                <p>Оцениваем рыночную стоимость, готовим объявление и продвигаем объект по нашим каналам.</p>
            </article>
            <article class="info-card service-card">
                <span class="service-icon"><i class="fas fa-key"></i></span>
                <h3>Аренда</h3>
                <p>Помогаем быстро найти надежных арендаторов или подобрать комфортное жилье в аренду.</p>
            </article>
            <article class="info-card service-card">
                <span class="service-icon"><i class="fas fa-scale-balanced"></i></span>
                <h3>Юридическое сопровождение</h3>
                <p>Проверяем чистоту сделки, отсутствие обременений и корректность всех договоров.</p>
            </article>
            <article class="info-card service-card">
                <span class="service-icon"><i class="fas fa-landmark"></i></span>
                <h3>Ипотечная консультация</h3>
                <p>Подскажем условия банков, соберем пакет документов и поможем пройти одобрение.</p>
            </article>
            <article class="info-card service-card">
                <span class="service-icon"><i class="fas fa-coins"></i></span>
                <h3>Инвестиции в недвижимость</h3>
                <p>Подбираем ликвидные объекты для сдачи в аренду и долгосрочного роста стоимости.</p>
            </article>
        </div>

        <div class="info-cta info-cta-rich">
            <h2>Нужна консультация по услугам?</h2>
            <p>Оставьте заявку через страницу контактов, и мы подберем удобный формат сотрудничества.</p>
            <a href="{{ route('contacts.index') }}" class="info-cta-button">Связаться с нами</a>
        </div>
    </div>
@endsection
