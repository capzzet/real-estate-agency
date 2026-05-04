@extends('layouts.app')

@section('title', 'Все сделки — Estate-KG')

@section('content')
    <div class="for-sale-page">
        <section class="for-sale-hero">
            <div class="for-sale-hero-text">
                <p class="for-sale-kicker">ВСЕ СДЕЛКИ В ОДНОМ ОКНЕ</p>
                <h1>Купить, снять, продать или сдать недвижимость</h1>
                <p>Одна команда для всех задач: подберем объект, найдем клиента, сопроводим сделку и защитим ваши интересы.</p>
                <div class="for-sale-hero-tags">
                    <span>Подбор для покупателя и арендатора</span>
                    <span>Юридическая защита</span>
                    <span>Сделка под ключ</span>
                </div>
            </div>
            <div class="for-sale-hero-stats">
                <div><strong>21 день</strong><span>средний срок закрытия</span></div>
                <div><strong>1200+</strong><span>успешных сделок</span></div>
                <div><strong>95%</strong><span>клиентов по рекомендациям</span></div>
            </div>
        </section>

        <section class="for-sale-benefits">
            <article>
                <i class="fas fa-camera-retro"></i>
                <h3>Профессиональная упаковка</h3>
                <p>Фото, описание и позиционирование объекта, чтобы выделиться среди конкурентов.</p>
            </article>
            <article>
                <i class="fas fa-bullhorn"></i>
                <h3>Мощное продвижение</h3>
                <p>Размещаем объект в ключевых каналах и приводим целевые обращения.</p>
            </article>
            <article>
                <i class="fas fa-file-contract"></i>
                <h3>Безопасная сделка</h3>
                <p>Проверяем документы и сопровождаем каждый этап до подписания.</p>
            </article>
            <article>
                <i class="fas fa-user-shield"></i>
                <h3>Фильтрация клиентов</h3>
                <p>Отсекаем неподходящие заявки и выводим только реальных кандидатов.</p>
            </article>
        </section>

        <section class="for-sale-form-section">
            <div class="for-sale-form-intro">
                <h2>Оставить заявку</h2>
                <p>Выберите цель, оставьте контакты — эксперт Estate-KG свяжется с вами в течение 15 минут в рабочее время.</p>
            </div>
            <form id="forSaleForm" action="{{ route('realty-hub.submit') }}" method="post" class="for-sale-form">
                @csrf
                <div class="for-sale-grid">
                    <select name="request_type" required>
                        <option value="">Что нужно?</option>
                        <option value="buy">Купить</option>
                        <option value="rent">Снять</option>
                        <option value="rent_out">Сдать в аренду</option>
                        <option value="sell">Продать</option>
                    </select>
                    <input type="text" name="name" placeholder="Ваше имя" required>
                    <input type="tel" name="phone" placeholder="Телефон" required>
                    <input type="text" name="city" placeholder="Город" required>
                    <input type="text" name="property_type" placeholder="Тип объекта (квартира, дом и т.д.)" required>
                    <input type="number" name="rooms" placeholder="Количество комнат">
                    <input type="number" name="budget" placeholder="Бюджет / желаемая цена">
                </div>
                <textarea name="comment" placeholder="Комментарий: район, срочность, особые условия"></textarea>
                <div class="for-sale-form-actions">
                    <button type="submit">Отправить заявку</button>
                    <a href="{{ route('contacts.index') }}">Нужна обычная консультация?</a>
                </div>
                <div id="forSaleSuccessMessage" class="success-message" style="display:none;"></div>
            </form>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('forSaleForm');
            const success = document.getElementById('forSaleSuccessMessage');
            if (!form || !success) {
                return;
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        request_type: form.querySelector('[name=request_type]').value,
                        name: form.querySelector('[name=name]').value,
                        phone: form.querySelector('[name=phone]').value,
                        city: form.querySelector('[name=city]').value,
                        property_type: form.querySelector('[name=property_type]').value,
                        rooms: form.querySelector('[name=rooms]').value,
                        budget: form.querySelector('[name=budget]').value,
                        comment: form.querySelector('[name=comment]').value
                    })
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            success.textContent = 'Заявка принята! Мы свяжемся с вами в ближайшее время.';
                            success.style.display = 'block';
                            form.reset();
                        } else {
                            success.textContent = 'Не удалось отправить заявку. Попробуйте еще раз.';
                            success.style.display = 'block';
                        }
                    })
                    .catch(() => {
                        success.textContent = 'Ошибка сети. Проверьте подключение и попробуйте снова.';
                        success.style.display = 'block';
                    });
            });
        });
    </script>
@endsection