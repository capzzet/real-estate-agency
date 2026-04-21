@extends('layouts.app')

@section('title', 'Контакты — Estate-KG')

@section('content')

    <div class="contact-container">
        <div class="contact-left">
            <h2>Контакты Estate-KG</h2>
            <p>Ответим на вопросы по покупке, аренде и продаже недвижимости. Заполните форму, и менеджер свяжется с вами в ближайшее время.</p>
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>г. Бишкек, ул. Киевская, 95</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <p><a href="mailto:info@estate-kg.kg">info@estate-kg.kg</a></p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone-alt"></i>
                    <p><a href="tel:+996700123456">+996 (700) 123-456</a></p>
                </div>
            </div>
            <div class="contact-info">
                <div class="contact-item">
                    <i class="far fa-clock"></i>
                    <p>Пн-Пт: 09:00 - 19:00, Сб: 10:00 - 16:00</p>
                </div>
            </div>
            <div class="social-media">
                <h3>Мы в соцсетях</h3>
                <ul class="social-icons">
                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                    <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                </ul>
            </div>
        </div>

        <div class="contact-right">
            <h2>Напишите нам</h2>
            <form id="contactForm" method="post" action="{{ route('contacts.submit') }}">
                @csrf
                <input type="text" name="name" placeholder="Имя" required>
                <input type="email" name="email" placeholder="Почта" required>
                <input type="tel" name="phone" placeholder="Телефон" required>
                <textarea name="message" placeholder="Расскажите, какая недвижимость вас интересует" required></textarea>
                <div class="form-buttons">
                    <button type="submit">Отправить</button>
                    <button type="reset">Очистить</button>
                </div>
            </form>
            <div id="contactSuccessMessage" class="success-message" style="display:none;"></div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('contactForm');
            const successMessage = document.getElementById('contactSuccessMessage');
            if (!form || !successMessage) {
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
                        name: form.querySelector('[name=name]').value,
                        email: form.querySelector('[name=email]').value,
                        phone: form.querySelector('[name=phone]').value,
                        message: form.querySelector('[name=message]').value,
                    })
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            successMessage.textContent = 'Сообщение успешно отправлено!';
                            successMessage.style.display = 'block';
                            form.reset();
                            setTimeout(() => { successMessage.style.display = 'none'; }, 3000);
                        } else {
                            successMessage.textContent = 'Не удалось отправить сообщение. Попробуйте еще раз.';
                            successMessage.style.display = 'block';
                        }
                    })
                    .catch(() => {
                        successMessage.textContent = 'Ошибка сети. Проверьте подключение и попробуйте снова.';
                        successMessage.style.display = 'block';
                    });
            });
        });
    </script>
@endsection
