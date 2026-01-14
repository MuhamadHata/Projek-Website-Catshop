<?php

// Start the session if it hasn't been started yet.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/Config.php';
// Include all necessary files for the application to function
require_once 'core/App.php';
require_once 'core/Controller.php';
require_once 'core/Koneksi.php';
require_once 'core/Flasher.php';
require_once __DIR__ . '/libraries/midtrans/Midtrans.php';