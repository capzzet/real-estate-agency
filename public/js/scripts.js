document.addEventListener("DOMContentLoaded", function() {
    console.log("Скрипт выполнен после загрузки контента");

    const modal = document.getElementById("callbackModal");
    const openModalButton = document.querySelector(".call a");
    const closeModalButton = document.getElementById("closeModal");
    const callbackForm = document.getElementById("callbackForm");
    const successMessage = document.getElementById("successMessage");

    if (modal && openModalButton && closeModalButton && callbackForm && successMessage) {
        console.log("Элементы модального окна найдены");

        openModalButton.addEventListener("click", function(event) {
            event.preventDefault();
            modal.style.display = "block";
            callbackForm.reset();
        });

        closeModalButton.addEventListener("click", function() {
            closeModal();
        });

        window.addEventListener("click", function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        callbackForm.addEventListener("submit", function(event) {
            event.preventDefault();

            const formData = new FormData(callbackForm);

            fetch(callbackForm.action, {
                method: callbackForm.method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.message === 'Успешно!') {
                        successMessage.style.display = "block";
                        callbackForm.style.display = "none";
                        setTimeout(closeModal, 3000);
                    } else {
                        alert('Ошибка при отправке формы.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при отправке формы.');
                });
        });

        function closeModal() {
            modal.style.display = "none";
            successMessage.style.display = "none";
            callbackForm.style.display = "block";
        }
    } else {
        console.log("Элементы модального окна не найдены");
    }

    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.main-nav ul li a');

    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });

    console.log("currentPath:", currentPath);
    console.log("navLinks:", navLinks);
});
