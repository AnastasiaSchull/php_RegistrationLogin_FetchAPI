document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("registration-form");
    const usernameInput = document.getElementById("username");
    const feedbackSpan = document.getElementById("usernameFeedback");
    const messageSpan = document.getElementById("message"); // получаем элемент

    if (!usernameInput || !feedbackSpan || !form) {
        console.error('One of the required elements is missing');
        return; //  если один из элементов не найден
    }

    // проверяем доступность имени пользователя при потере фокуса
    usernameInput.addEventListener("blur", function() {
        const username = usernameInput.value;
        if (username) {
            fetch(`registration.php?username=${encodeURIComponent(username)}`)//encodeURIComponent кодирует компоненты URI,например робелы преобразует в %20
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        feedbackSpan.textContent = "Username already exists";
                        feedbackSpan.style.color = "red";
                    } else {
                        feedbackSpan.textContent = "Username is available";
                        feedbackSpan.style.color = "green";
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });
    // обработчик для отправки формы
    form.addEventListener("submit", function(event) {
        event.preventDefault();
        const data = {
            username: document.getElementById("username").value,
            password: document.getElementById("password").value
        };
        console.log("Sending data:", data);
        fetch('registration.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)// преобразует объект JS в строку JSON
        })
            .then(response => response.json())
            .then(data => {
               // alert(data.message);
                messageSpan.textContent = data.message;
                messageSpan.style.display = "block"; //показываем сообщение
                if (data.success) {
                    setTimeout(() => {  // скрываем сообщение через 2 сек
                        messageSpan.style.display = "none";
                        window.location.href = 'index.php'; // на главную
                    }, 2000);
                }
            })
            .catch(error => console.error('Error:', error));
    });
});
