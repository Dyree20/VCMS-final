<div id="addPanel" class="dialog-overlay hidden">
    <div class="dialog-box">
        <h3>Add New Clamping</h3>
        <form class="form-grid" id="clampingForm" action="{{ route('clampings') }}" method="POST" enctype="multipart/form-data">
         @csrf
            <div class="form-fields">
                <label>Vehicle Plate Number:
                    <input type="text" name="plate_no" required>
                </label>
                <label>Vehicle Type:
                    <input type="text" name="vehicle_type" required>
                </label>
                <label>Reason for Clamping:
                    <input type="text" name="reason" required>
                </label>
                <label>Location:
                    <input type="text" name="location" required>
                </label>
                <label>Fine Amount:
                    <input type="text" name="fine_amount" required>
                </label>
            </div>

            <!-- Upload box -->
            <div class="upload-box">
                <input type="file" id="photoInput" name="photo" accept="image/*" capture="camera" hidden>
                <label for="photoInput" class="upload-label">
                    <span class="plus-icon">+</span>
                    <p>Upload Photo</p>
                </label>
                <div id="photoPreview" class="photo-preview hidden">
                    <img id="previewImg" src="" alt="Preview">
                </div>
            </div>

            <div class="dialog-actions">
                <button type="submit" class="btn btn-save">Save</button>
                <button type="button" id="closeBtn" class="btn btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

@include('partials.overlay')

<script>
    const photoInput = document.getElementById('photoInput');
    const previewImg = document.getElementById('previewImg');
    const photoPreview = document.getElementById('photoPreview');

    photoInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                photoPreview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
</script>
