document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");

    // Overlay elements
    const loginOverlay = document.getElementById("loginOverlay");
    const loginSpinner = document.getElementById("loginSpinner");
    const loginSuccess = document.getElementById("loginSuccess");
    const loginMessage = document.getElementById("loginMessage");
    const loginSub = document.getElementById("loginSub");

    if (loginForm) {
        loginForm.addEventListener("submit", (e) => {
            e.preventDefault(); // Prevent default to show overlay first

            // Show overlay and spinner
            loginOverlay.classList.remove("hidden");
            loginSpinner.style.display = "block";
            loginSuccess.style.display = "none";
            loginMessage.textContent = "Logging in...";
            loginSub.textContent = "";

            // Submit the form normally after short delay to show spinner
            setTimeout(() => {
                loginForm.submit(); // normal session-based login
            }, 500); // 0.5 second delay
        });
    }

    // Auto-hide login error popup after 4 seconds
    const loginErrorPopup = document.getElementById("loginErrorPopup");
    if (loginErrorPopup) {
        setTimeout(() => {
            loginErrorPopup.style.display = "none";
        }, 4000);
    }
});
