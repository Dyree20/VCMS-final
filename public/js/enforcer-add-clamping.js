document.addEventListener("DOMContentLoaded", () => {
    const takePhotoBtn = document.getElementById('takePhotoBtn');
    const photoInput = document.getElementById('photo');
    const preview = document.getElementById('preview');
    const form = document.getElementById('clampingForm');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    photoInput.style.display = "none";

    // Open camera
    takePhotoBtn.addEventListener('click', () => {
        photoInput.click();
    });

    // Preview the photo
    photoInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });

    // ✅ Popup helper
    function showPopup(message, id = null) {
        const popup = document.getElementById("successPopup");
        const popupMessage = document.getElementById("popupMessage");
        popupMessage.innerHTML = `
            <p>${message}</p>
            ${id ? `<button id="printReceiptBtn" style="margin-top: 15px; padding: 12px 24px; background: #007bff; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px;">🖨️ Print Receipt</button>` : ""}
        `;
        popup.style.display = "flex";

        if (id) {
            // Remove any existing listeners
            const oldBtn = document.getElementById("printReceiptBtn");
            if (oldBtn) {
                oldBtn.replaceWith(oldBtn.cloneNode(true));
            }
            // Print button handler
            setTimeout(() => {
                const printBtn = document.getElementById("printReceiptBtn");
                if (printBtn) {
                    printBtn.addEventListener("click", () => {
                window.open(`/clampings/receipt/${id}`, "_blank");
            });
                }
            }, 100);
        }

        // Auto close popup after delay (only if no print button or after longer delay)
        setTimeout(() => {
            if (!id) {
            popup.style.display = "none";
            window.location.href = "/enforcer/dashboard";
            }
        }, id ? 10000 : 4000);
    }

    // ✅ Form submission
    let isSubmitting = false; // Prevent double submission
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        e.stopPropagation();

        // Prevent double submission
        if (isSubmitting) {
            return;
        }

        isSubmitting = true;
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Adding...';
        }

        const formData = new FormData(form);

        try {
            const response = await fetch(window.clampingsRoute, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                body: formData,
                credentials: 'include'
            });

            let result;
            try {
                result = await response.json();
            } catch {
                result = { success: response.ok, message: "Clamping added successfully!" };
            }

            if (result.success) {
                // ✅ Reset form to prevent duplicate submission
                form.reset();
                preview.style.display = 'none';
                
                // ✅ Show success popup with print receipt option
                showPopup(result.message || "Clamping added successfully!", result.id);
            } else {
                showPopup(result.message || "Failed to add clamping record.");
                isSubmitting = false;
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'ADD CLAMPING';
                }
            }

        } catch (error) {
            console.error(error);
            showPopup("An error occurred while submitting the form.");
            isSubmitting = false;
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'ADD CLAMPING';
            }
        }
    });
});
