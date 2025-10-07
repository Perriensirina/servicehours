let startTime, timerInterval;

window.addEventListener("DOMContentLoaded", function () {
    const startBtn = document.getElementById("startTimer");
    const stopBtn = document.getElementById("stopTimer");
    const timerDisplay = document.getElementById("timer");
    const elapsedInput = document.getElementById("elapsed_time");

    if (localStorage.getItem("timerRunning") === "true") {
        startTime = parseInt(localStorage.getItem("startTime"));
        startTimer();
        startBtn.disabled = true;
        stopBtn.disabled = false;
    }

    startBtn.addEventListener("click", () => {
        startTime = Date.now();
        localStorage.setItem("startTime", startTime);
        localStorage.setItem("timerRunning", "true");
        startTimer();
        startBtn.disabled = true;
        stopBtn.disabled = false;
    });

    stopBtn.addEventListener("click", () => {
        clearInterval(timerInterval);
        localStorage.setItem("timerRunning", "false");
        startBtn.disabled = false;
        stopBtn.disabled = true;

        const totalElapsed = Date.now() - startTime;
        elapsedInput.value = Math.floor(totalElapsed / 1000); // seconds
    });

    function startTimer() {
        timerInterval = setInterval(() => {
            const elapsed = Date.now() - startTime;
            timerDisplay.textContent = formatTime(elapsed);
        }, 1000);
    }

    function formatTime(ms) {
        const totalSeconds = Math.floor(ms / 1000);
        const hrs = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
        const mins = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
        const secs = (totalSeconds % 60).toString().padStart(2, '0');
        return `${hrs}:${mins}:${secs}`;
    }
});


    document.addEventListener("DOMContentLoaded", () => {
        const popup = document.querySelector(".popup-alert");
        if (popup) {
            setTimeout(() => {
                popup.style.opacity = "0";
                popup.style.transform = "translateY(-20px)";
                setTimeout(() => popup.remove(), 500); // remove after animation
            }, 3000); // 3 seconds
        }
    });


