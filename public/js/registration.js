// Show overlay (loading)
function showOverlay(message = "Saving...", subText = "") {
    const overlay = document.getElementById('submitOverlay');
    const spinner = document.getElementById('overlaySpinner');
    const success = document.getElementById('overlaySuccess');
    const overlayMessage = document.getElementById('overlayMessage');
    const overlaySub = document.getElementById('overlaySub');

    overlay.classList.remove('hidden');
    spinner.style.display = 'block';
    success.style.display = 'none';
    overlayMessage.textContent = message;
    overlaySub.textContent = subText;
}

// Show result (success or error)
function showOverlayResult(isSuccess, message, subText = "") {
    const overlay = document.getElementById('submitOverlay');
    const spinner = document.getElementById('overlaySpinner');
    const success = document.getElementById('overlaySuccess');
    const overlayMessage = document.getElementById('overlayMessage');
    const overlaySub = document.getElementById('overlaySub');

    spinner.style.display = 'none';
    success.style.display = 'block';
    success.textContent = isSuccess ? "✅" : "❌";
    success.style.color = isSuccess ? "green" : "red";

    overlayMessage.textContent = message;
    overlaySub.textContent = subText;

    // Auto hide overlay
    setTimeout(() => {
        overlay.classList.add('hidden');
    }, isSuccess ? 2000 : 4000);
}

// Attach registration form submit
document.addEventListener('DOMContentLoaded', () => {
    const registrationForm = document.querySelector('.auth-register-container form');
    if (!registrationForm) return;

    registrationForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        showOverlay("Registering...", "Please wait...");

        const formData = new FormData(registrationForm);

        try {
            const response = await fetch(registrationForm.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                    "Accept": "application/json"
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                showOverlayResult(true, "Registration Successful!", "Your account is pending approval by an administrator. You will be able to log in once approved.");
                registrationForm.reset();
                // Optionally, you can keep the overlay visible a bit longer or let the user close it manually
            } else {
                let errMsg = "Registration failed!";
                if (data.errors) {
                    errMsg = Object.values(data.errors).flat().join("\n");
                } else if (data.message) {
                    errMsg = data.message;
                }
                showOverlayResult(false, "Registration Failed!", errMsg);
            }
        } catch (error) {
            console.error("Error:", error);
            showOverlayResult(false, "Something went wrong!", "Please try again.");
        }
    });
});
