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

    const aiAssistant = document.getElementById("aiAssistant");
    const aiToggle = document.getElementById("aiAssistantToggle");
    const aiPanel = document.getElementById("aiAssistantPanel");
    const aiClose = document.getElementById("aiAssistantClose");
    const aiReset = document.getElementById("aiAssistantReset");
    const aiForm = document.getElementById("aiAssistantForm");
    const aiInput = document.getElementById("aiAssistantInput");
    const aiMessages = document.getElementById("aiAssistantMessages");

    if (aiAssistant && aiToggle && aiPanel && aiClose && aiForm && aiInput && aiMessages) {
        const messagesStorageKey = "estate_ai_chat_messages";
        const getChatId = function() {
            const storageKey = "estate_ai_chat_id";
            let current = window.localStorage.getItem(storageKey);
            if (!current) {
                current = "chat_" + Date.now() + "_" + Math.random().toString(36).slice(2, 9);
                window.localStorage.setItem(storageKey, current);
            }
            return current;
        };

        const saveMessages = function() {
            const messages = [];
            aiMessages.querySelectorAll(".ai-message").forEach(function(node) {
                messages.push({
                    type: node.classList.contains("ai-message-user") ? "user" : "bot",
                    text: node.textContent || ""
                });
            });
            window.localStorage.setItem(messagesStorageKey, JSON.stringify(messages));
        };

        const loadMessages = function() {
            const raw = window.localStorage.getItem(messagesStorageKey);
            if (!raw) {
                return false;
            }
            try {
                const parsed = JSON.parse(raw);
                if (!Array.isArray(parsed) || !parsed.length) {
                    return false;
                }
                aiMessages.innerHTML = "";
                parsed.forEach(function(item) {
                    if (!item || typeof item.text !== "string") {
                        return;
                    }
                    const type = item.type === "user" ? "user" : "bot";
                    appendMessage(item.text, type);
                });
                return true;
            } catch (_e) {
                return false;
            }
        };

        const appendMessage = function(text, type) {
            const item = document.createElement("div");
            item.className = "ai-message " + (type === "user" ? "ai-message-user" : "ai-message-bot");
            item.textContent = text;
            aiMessages.appendChild(item);
            aiMessages.scrollTop = aiMessages.scrollHeight;
            saveMessages();
        };

        if (!loadMessages()) {
            saveMessages();
        }

        const openAssistant = function() {
            aiPanel.hidden = false;
            aiAssistant.classList.add("is-open");
            aiToggle.setAttribute("aria-expanded", "true");
            setTimeout(function() {
                aiInput.focus();
            }, 100);
        };

        const closeAssistant = function() {
            aiPanel.hidden = true;
            aiAssistant.classList.remove("is-open");
            aiToggle.setAttribute("aria-expanded", "false");
        };

        aiToggle.addEventListener("click", function() {
            if (aiPanel.hidden) {
                openAssistant();
            } else {
                closeAssistant();
            }
        });

        aiClose.addEventListener("click", closeAssistant);
        if (aiReset) {
            aiReset.addEventListener("click", function() {
                fetch("/ai-assistant/reset", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({})
                })
                    .then(function(response) {
                        return response.json().then(function(data) {
                            return { ok: response.ok, data: data };
                        });
                    })
                    .then(function(result) {
                        window.localStorage.setItem("estate_ai_chat_id", "chat_" + Date.now() + "_" + Math.random().toString(36).slice(2, 9));
                        window.localStorage.removeItem(messagesStorageKey);
                        aiMessages.innerHTML = "";
                        appendMessage("Привет! Я помогу с выбором недвижимости, услугами и заявками. Напишите ваш вопрос.", "bot");
                        if (result.ok && result.data.success) {
                            appendMessage(result.data.message, "bot");
                        }
                    })
                    .catch(function() {
                        appendMessage("Не удалось очистить диалог. Попробуйте еще раз.", "bot");
                    });
            });
        }

        aiForm.addEventListener("submit", function(event) {
            event.preventDefault();
            const userText = aiInput.value.trim();
            if (!userText) {
                return;
            }

            appendMessage(userText, "user");
            aiInput.value = "";
            appendMessage("Печатаю ответ...", "bot");

            fetch("/ai-assistant/chat", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    message: userText,
                    chat_id: getChatId()
                })
            })
                .then(function(response) {
                    return response.json().then(function(data) {
                        return { ok: response.ok, data: data };
                    });
                })
                .then(function(result) {
                    aiMessages.removeChild(aiMessages.lastElementChild);
                    if (!result.ok || !result.data.success) {
                        appendMessage(result.data.message || "Ошибка AI-сервиса. Попробуйте чуть позже.", "bot");
                        return;
                    }
                    appendMessage(result.data.message, "bot");
                })
                .catch(function() {
                    aiMessages.removeChild(aiMessages.lastElementChild);
                    saveMessages();
                    appendMessage("Не удалось связаться с AI. Проверьте подключение и попробуйте снова.", "bot");
                });
        });
    }
});
