/**
 * Инициализация скрипта после загрузки страницы
 */
window.onload = function() {
    if (typeof LiveSearchInput === "undefined" || LiveSearchInput.tagName != "INPUT") {
        console.error("Error LiveSearch: Can't find <input type='text' id ='LiveSearchInput'></input>");
    }
    else if (typeof LiveSearchResults == 'undefined' || LiveSearchResults.tagName != "DIV") {
        console.error("Error LiveSearch: Can't find <div id ='LiveSearchResults'></div>");
    }
    else {
        // Вешаем обработчик на изменение текста
        LiveSearchInput.oninput = OnInputTextChangeHandler;
    }
}

/**
 * Обработчик изменения текста
 */
function OnInputTextChangeHandler() {
    if (!LiveSearchInput.value.trim()) return;
    if (request = window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest()) {
        // Вешаем обработчик ответа от сервера
        request.onload = ServerResponseHandler;
        // Вешаем обработчик ошибки запроса
        request.onerror = RequestErrorHandler;
        // Инициализируем соеденение
        request.open("post", "app.php", true);
        // Устанавливаем заголовок с типом отправляемых данных json
        request.setRequestHeader("Content-Type","application/json; charset=utf-8");
        // Отправляем запрос на сервер
        request.send(JSON.stringify( { 
            'query' : LiveSearchInput.value.trim(),
            'limit' : 100 }));
    }
    else {
        console.error("Error LiveSearch: Can't create XMLHttpRequest");
    }
}

/**
 * Обработчик ответа от сервера
 */
function ServerResponseHandler() {
    LiveSearchResults.innerHTML = "";
    JSON.parse(this.response, function(key, value) {
        if (key !== "" && value) LiveSearchResults.innerHTML += "<li>" + value + "</li>";
    });
    LiveSearchResults.innerHTML = "<ul>" + LiveSearchResults.innerHTML + "</ul>";
}

/**
 * Обработчик ошибки отправки запроса на сервер
 */
function RequestErrorHandler() {
    console.error("Error LiveSearch: Request error");
    LiveSearchResults.innerHTML = "Request error";
}