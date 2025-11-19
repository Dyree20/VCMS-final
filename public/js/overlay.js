document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("clampingForm");
    const overlay = document.getElementById("submitOverlay");
    const overlayMsg = document.getElementById("overlayMessage");
    const overlaySub = document.getElementById("overlaySub");

    if (form) {
        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            overlay.classList.remove("hidden");
            overlayMsg.textContent = "Saving...";
            overlaySub.textContent = "";

            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    body: formData
                });
                const data = await response.json();

                if (response.ok && data.success) {
                    overlayMsg.textContent = "✅ Clamping added successfully!";
                    overlaySub.innerHTML = `
                        <button id="printReceiptBtn" class="btn btn-print">🖨️ Print Receipt</button>
                        <p>or wait to be redirected...</p>
                    `;

                    setTimeout(() => {
                        window.location.href = "/clampings";
                    }, 5000);

                    // Handle print button click
                    document.getElementById("printReceiptBtn").addEventListener("click", () => {
                        window.open(`/clampings/receipt/${data.id}`, "_blank");
                    });
                } else {
                    overlayMsg.textContent = "⚠️ Failed to save.";
                    overlaySub.textContent = data.message || "";
                    setTimeout(() => overlay.classList.add("hidden"), 2000);
                }
            } catch (error) {
                overlayMsg.textContent = "❌ Error occurred.";
                overlaySub.textContent = error.message;
                setTimeout(() => overlay.classList.add("hidden"), 2000);
            }
        });
    }
});
