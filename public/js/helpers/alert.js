export class alert {
    errorWindowShow(window, message) {
        window.text(message);
        window.addClass('show');

        setTimeout(function () {
            window.removeClass('show');
        }, 3000);
    }

    startTimer(button) {
        const endTime = new Date();
        endTime.setMinutes(endTime.getMinutes() + 1);

        const timerInterval = setInterval(function () {
            const now = new Date();
            const timeRemaining = endTime - now;

            if (timeRemaining <= 0) {
                button.text('Отправить еще раз');
                clearInterval(timerInterval);
            } else {
                const minutes = Math.floor(timeRemaining / 60000);
                const seconds = Math.floor((timeRemaining % 60000) / 1000);

                button.text(minutes + ':' + seconds);
            }
        }, 1000);
    }
}
