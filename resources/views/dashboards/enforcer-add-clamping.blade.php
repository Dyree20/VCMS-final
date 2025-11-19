<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Add Clamping</title>

  <link rel="stylesheet" href="/../../styles/enforcer-add-clamping.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
</head>
<body>

  <div class="container">
    <div class="top-bar">
      <i class="fa-solid fa-arrow-left" id="backBtn"></i>
      <h2>Add Clamping</h2>
    </div>

    <form id="clampingForm" action="{{ route('clampings') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <label for="plate">Plate Number</label>
  <input type="text" id="plate" name="plate_no" placeholder="Enter plate number" required>

  <label for="vehicle">Vehicle Type</label>
  <input type="text" id="vehicle" name="vehicle_type" placeholder="Enter vehicle type" required>

  <label for="location">Location</label>
  <input type="text" id="location" name="location" placeholder="Enter clamping location" required>

  <label for="reason">Reason</label>
  <textarea id="reason" name="reason" placeholder="Enter reason for clamping" required></textarea>

  <label for="fine">Fine Amount</label>
  <div class="icon-input">
    <input type="number" id="fine" name="fine_amount" placeholder="Enter fine amount" required>
    <i class="fa-solid fa-peso-sign"></i>
  </div>

  <label for="photo">Photo</label>
    <div class="photo-section">
      <button type="button" id="takePhotoBtn">
        <i class="fa-solid fa-camera"></i> Take Photo
      </button>
      <input type="file" id="photo" name="photo" accept="image/*" capture="environment">
      <img id="preview" alt="Photo Preview" style="display:none; width:100%; border-radius:10px;">
    </div>

  <button type="submit">ADD CLAMPING</button>

  <!-- Success Popup -->
  <div id="successPopup" class="popup">
    <div class="popup-content">
      <i class="fa-solid fa-circle-check"></i>
      <p id="popupMessage">Clamping added successfully!</p>
    </div>
  </div>


</form>
    <script>
        window.clampingsRoute = "{{ route('clampings') }}";
    </script>
     <script src="/../../js/navigation.js"></script>
     <script src="/../../js/enforcer-add-clamping.js"></script>
  </div>

</body>
</html>

