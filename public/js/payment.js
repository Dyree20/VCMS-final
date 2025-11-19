document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("walkinPaymentForm");
    const overlay = document.getElementById("paymentOverlay");
    const overlayMsg = document.getElementById("paymentMessage");
    const overlaySub = document.getElementById("paymentSub");
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    if (form) {
        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            overlay.classList.remove("hidden");
            overlayMsg.textContent = "💾 Saving payment...";
            overlaySub.textContent = "";

            const formData = new FormData(form);
            formData.append("_token", csrfToken);

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    body: formData,
                });

                const data = await response.json();

                if (!response.ok) {
                    //  Error Handling
                    if (response.status === 400 && data.message?.includes("already been paid")) {
                        overlayMsg.textContent = "⚠️ Ticket Already Paid";
                        overlaySub.textContent = "This ticket cannot be paid again.";
                    } else if (data.errors) {
                        overlayMsg.textContent = "⚠️ Validation Error";
                        const firstField = Object.keys(data.errors)[0];
                        overlaySub.textContent = data.errors[firstField][0];
                    } else {
                        overlayMsg.textContent = "⚠️ Payment Failed";
                        overlaySub.textContent = data.message || "Unknown error";
                    }
                    setTimeout(() => overlay.classList.add("hidden"), 3000);
                    return;
                }

                // Success
                if (data.success) {
                    overlayMsg.textContent = "✅ Payment recorded successfully!";
                    overlaySub.textContent = "Redirecting...";
                    setTimeout(() => {
                        window.location.href = "/payments";
                    }, 2000);
                } else {
                    overlayMsg.textContent = "⚠️ Failed to save payment.";
                    overlaySub.textContent = data.message || "Unknown error.";
                    setTimeout(() => overlay.classList.add("hidden"), 3000);
                }
            } catch (error) {
                overlayMsg.textContent = "❌ Server or network error.";
                overlaySub.textContent = error.message;
                setTimeout(() => overlay.classList.add("hidden"), 3000);
            }
        });
    }
});
