function getBaseUrl() {
  return "http://localhost/PHP%20Project/vehicle-booking";
}

const API_URL = `${getBaseUrl()}/api`;

// Helper function for API calls
async function apiCall(endpoint, method = "GET", data = null) {
  const options = {
    method,
    headers: {
      "Content-Type": "application/json",
    },
  };

  // Add auth token if exists
  const token = localStorage.getItem("auth_token");
  if (token) {
    options.headers["Authorization"] = `Bearer ${token}`;
  }

  // Add body for POST/PUT requests
  if (data && (method === "POST" || method === "PUT")) {
    options.body = JSON.stringify(data);
  }

  try {
    const response = await fetch(`${API_URL}${endpoint}`, options);
    const responseText = await response.text();
    let result = {};

    try {
      result = responseText ? JSON.parse(responseText) : {};
    } catch (error) {
      result = { message: responseText || "Unexpected server response" };
    }

    if (!response.ok) {
      throw new Error(result.message || "API request failed");
    }

    return result;
  } catch (error) {
    console.error("API Error:", error);
    throw error;
  }
}

// Check if user is logged in
function checkAuth() {
  const token = localStorage.getItem("auth_token");
  if (!token) {
    window.location.href = "login.html";
  }
}

// Logout function
function logout() {
  localStorage.removeItem("auth_token");
  localStorage.removeItem("user_data");
  window.location.href = "login.html";
}
