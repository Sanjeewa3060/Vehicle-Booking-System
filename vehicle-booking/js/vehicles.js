window.addEventListener("DOMContentLoaded", () => {
  checkAuth();
  loadVehicles();
});

// Load all vehicles
async function loadVehicles() {
  try {
    const result = await apiCall("vehicles/list.php");

    if (result.success) {
      displayVehicles(result.data);
    }
  } catch (error) {
    console.error("Failed to load vehicles:", error);
  }
}

// Display vehicles in grid
function displayVehicles(vehicles) {
  const grid = document.getElementById("vehiclesGrid");
  grid.innerHTML = "";

  vehicles.forEach((vehicle) => {
    const card = document.createElement("div");
    card.className = "vehicle-card";

    const statusClass =
      vehicle.status === "Available"
        ? "status-available"
        : "status-unavailable";

    card.innerHTML = `
      <div class="vehicle-image">🚗</div>
      <div class="vehicle-info">
        <h3>${vehicle.name}</h3>
        <p>${vehicle.type}</p>
        <div class="vehicle-footer">
          <span>$${vehicle.price}/day</span>
          <span class="status-badge ${statusClass}">
            ${vehicle.status}
          </span>
        </div>
        <button 
        class="btn btn-accent btn-block"
          onclick="bookVehicle(${vehicle.id})"
          ${vehicle.status !== "Available" ? "disabled" : ""}
        >
          Book Now
        </button>
      </div>
    `;

    grid.appendChild(card);
  });
}

function bookVehicle(id) {
  window.location.href = `booking.html?vehicle_id=${id}`;
}
