checkAuth();

// Get vehicle ID from URL
const urlParams = new URLSearchParams(window.location.search);
const vehicleId = urlParams.get("vehicle_id");

// Load vehicle details
async function loadVehicleDetails() {
  if (!vehicleId) {
    window.location.href = "vehicles.html";
    return;
  }

  try {
    const result = await apiCall(`vehicles/details.php?id=${vehicleId}`);

    if (result.success) {
      displayVehicleDetails(result.data);
    }
  } catch (error) {
    console.error("Failed to load vehicle details:", error);
  }
}

// Display vehicle details
function displayVehicleDetails(vehicle) {
  const detailsDiv = document.getElementById("vehicleDetails");
  detailsDiv.innerHTML = `
        <div class="vehicle-details-icon">🚗</div>
        <div class="vehicle-details-info">
            <h2>${vehicle.name}</h2>
            <p>${vehicle.type}</p>
            <p class="vehicle-details-price">$${vehicle.price}/day</p>
        </div>
    `;

  document.getElementById("vehicleId").value = vehicle.id;
}

// Handle booking form submission
document
  .getElementById("bookingForm")
  ?.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const data = {
      vehicle_id: formData.get("vehicle_id"),
      start_date: formData.get("start_date"),
      end_date: formData.get("end_date"),
    };

    // Validate dates
    const startDate = new Date(data.start_date);
    const endDate = new Date(data.end_date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (startDate < today) {
      alert("Start date cannot be in the past!");
      return;
    }

    if (endDate <= startDate) {
      alert("End date must be after start date!");
      return;
    }

    try {
      const result = await apiCall("bookings/create.php", "POST", data);

      if (result.success) {
        alert("Booking confirmed successfully!");
        window.location.href = "booking-history.html";
      } else {
        alert(result.message || "Booking failed");
      }
    } catch (error) {
      alert("Booking failed. Please try again.");
    }
  });

// Load vehicle details on page load
window.addEventListener("DOMContentLoaded", loadVehicleDetails);
