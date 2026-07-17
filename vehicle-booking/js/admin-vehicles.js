checkAuth();

// Check if user is admin
function checkAdminAccess() {
  const userData = localStorage.getItem("user_data");
  if (!userData) {
    window.location.href = "login.html";
    return false;
  }

  const user = JSON.parse(userData);
  if (user.role !== "admin") {
    alert("Access denied: Admin only");
    window.location.href = "user-dashboard.html";
    return false;
  }
  return true;
}

if (!checkAdminAccess()) {
  throw new Error("Unauthorized access");
}

let currentEditingVehicle = null;

// Load all vehicles
async function loadVehicles() {
  try {
    const result = await apiCall("/admin/vehicles/list.php");

    if (result.success) {
      displayVehicles(result.data);
    }
  } catch (error) {
    console.error("Failed to load vehicles:", error);
  }
}

// Display vehicles in table
function displayVehicles(vehicles) {
  const tbody = document.getElementById("vehiclesTableBody");
  tbody.innerHTML = "";

  vehicles.forEach((vehicle) => {
    const row = document.createElement("tr");
    const statusClass =
      vehicle.status === "Available"
        ? "status-available"
        : "status-unavailable";

    row.innerHTML = `
            <td>${vehicle.name}</td>
            <td>${vehicle.type}</td>
            <td>$${vehicle.price}</td>
            <td><span class="status-badge ${statusClass}">${vehicle.status}</span></td>
            <td>
                <div class="table-actions">
                    <button class="action-btn action-btn-edit" onclick="editVehicle(${vehicle.id})">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>
                    <button class="action-btn action-btn-delete" onclick="deleteVehicle(${vehicle.id})">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                    </button>
                </div>
            </td>
        `;

    tbody.appendChild(row);
  });
}

// Show modal for adding vehicle
function showAddModal() {
  currentEditingVehicle = null;
  document.getElementById("modalTitle").textContent = "Add Vehicle";
  document.getElementById("vehicleForm").reset();
  document.getElementById("vehicleModal").classList.remove("hidden");
}

// Edit vehicle
async function editVehicle(id) {
  try {
    const result = await apiCall(`/admin/vehicles/details.php?id=${id}`);

    if (result.success) {
      currentEditingVehicle = result.data;
      document.getElementById("modalTitle").textContent = "Edit Vehicle";
      document.getElementById("vehicleName").value = result.data.name;
      document.getElementById("vehicleType").value = result.data.type;
      document.getElementById("vehiclePrice").value = result.data.price;
      document.getElementById("vehicleStatus").value = result.data.status;
      document.getElementById("vehicleModal").classList.remove("hidden");
    }
  } catch (error) {
    alert("Failed to load vehicle details");
  }
}

// Delete vehicle
async function deleteVehicle(id) {
  if (!confirm("Are you sure you want to delete this vehicle?")) {
    return;
  }

  try {
    const result = await apiCall("/admin/vehicles/delete.php", "POST", { id });

    if (result.success) {
      alert("Vehicle deleted successfully");
      loadVehicles();
    } else {
      alert(result.message || "Failed to delete vehicle");
    }
  } catch (error) {
    alert("Failed to delete vehicle");
  }
}

// Handle vehicle form submission
document
  .getElementById("vehicleForm")
  ?.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const data = {
      name: formData.get("name"),
      type: formData.get("type"),
      price: formData.get("price"),
      status: formData.get("status"),
    };

    const endpoint = currentEditingVehicle
      ? "/admin/vehicles/update.php"
      : "/admin/vehicles/create.php";

    if (currentEditingVehicle) {
      data.id = currentEditingVehicle.id;
    }

    try {
      const result = await apiCall(endpoint, "POST", data);

      if (result.success) {
        alert(
          currentEditingVehicle
            ? "Vehicle updated successfully"
            : "Vehicle added successfully",
        );
        closeModal();
        loadVehicles();
      } else {
        alert(result.message || "Operation failed");
      }
    } catch (error) {
      alert("Operation failed");
    }
  });

// Close modal
function closeModal() {
  document.getElementById("vehicleModal").classList.add("hidden");
  document.getElementById("vehicleForm").reset();
  currentEditingVehicle = null;
}

window.addEventListener("DOMContentLoaded", loadVehicles);
