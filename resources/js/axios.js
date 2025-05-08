import axios from "axios";

// Function to set the content type
// const contentType = (appType) => {
//     axios.defaults.headers.post["Content-Type"] = appType;
// };

// Set default base URL
axios.defaults.baseURL = "http://127.0.0.1:8000/api/";
// axios.defaults.baseURL = "https://omary.arzantelecom.com/api/";

// Retrieve the token from session storage

// Set the Authorization header with the Bearer token
// axios.defaults.headers.common["Authorization"] =
//     "Bearer " + sessionStorage.getItem("token");

// Export axios and setContentType
export { axios };

//