@extends('layouts.app')

@section('title', 'Отзывы — Estate-KG')

@section('content')
    <div class="reviews-page">
        <div class="reviews-header">
            <h1>Отзывы клиентов</h1>
            <p>Поделитесь своим опытом работы с Estate-KG. Нам важно ваше мнение.</p>
        </div>

        <div class="reviews-layout">
            <div class="reviews-list">
                @forelse($reviews as $review)
                    <article class="review-card">
                        <div class="review-card-top">
                            <h3>{{ $review->name }}</h3>
                            <span class="review-rating">{{ str_repeat('★', (int) $review->rating) }}</span>
                        </div>
                        <p class="review-meta">
                            {{ $review->city ?: 'Бишкек' }} · {{ $review->created_at->format('d.m.Y') }}
                        </p>
                        <p>{{ $review->message }}</p>
                    </article>
                @empty
                    <div class="review-empty">
                        Пока нет отзывов. Будьте первым, кто оставит отзыв.
                    </div>
                @endforelse
            </div>

            <div class="review-form-wrap">
                <h2>Оставить отзыв</h2>
                <form id="reviewForm" action="{{ route('reviews.submit') }}" method="post">
                    @csrf
                    <input type="text" name="name" placeholder="Ваше имя" required>
                    <input type="text" name="city" placeholder="Город (необязательно)">
                    <select name="rating" required>
                        <option value="">Оценка</option>
                        <option value="5">5 - Отлично</option>
                        <option value="4">4 - Хорошо</option>
                        <option value="3">3 - Нормально</option>
                        <option value="2">2 - Плохо</option>
                        <option value="1">1 - Очень плохо</option>
                    </select>
                    <textarea name="message" placeholder="Ваш отзыв" required></textarea>
                    <button type="submit">Отправить отзыв</button>
                </form>
                <div id="reviewSuccessMessage" class="success-message" style="display:none;"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('reviewForm');
            const successMessage = document.getElementById('reviewSuccessMessage');
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
                        city: form.querySelector('[name=city]').value,
                        rating: form.querySelector('[name=rating]').value,
                        message: form.querySelector('[name=message]').value
                    })
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            successMessage.textContent = 'Спасибо! Ваш отзыв успешно отправлен.';
                            successMessage.style.display = 'block';
                            form.reset();
                            setTimeout(() => { successMessage.style.display = 'none'; }, 3000);
                            setTimeout(() => { window.location.reload(); }, 900);
                        } else {
                            successMessage.textContent = 'Не удалось отправить отзыв. Попробуйте еще раз.';
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