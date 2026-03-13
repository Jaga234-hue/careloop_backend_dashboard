<?php
// config.php - Configuration for the Doctor Dashboard
// This file reads the API_URL from the environment variable, 
// which is useful for Docker/Render deployments.

$apiUrl = getenv('API_URL') ?: 'http://localhost:8000';

// We'll inject this into a JavaScript variable that all pages can use
echo "<script>const API_BASE_URL = '" . rtrim($apiUrl, '/') . "';</script>";
?>
