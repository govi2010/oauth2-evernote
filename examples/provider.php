<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/EvernoteOauth.php';
//require __DIR__ . '/../src/EvernoteOauth_old.php';
use  League\OAuth2\Client\Provider\EvernoteOauth;

// Replace these with your token settings
// Create a project at https://console.developers.google.com/
$consumerKey = '';
$consumerSecret = '';

// Change this if you are not using the built-in PHP server
$redirectUrl = 'http://localhost:8081/oauth2-evernote/examples/';

$sandbox=true;
// Start the session
session_start();

// Initialize the provider
$provider = new EvernoteOauth(compact('consumerKey', 'consumerSecret', 'redirectUrl','sandbox'));

// No HTML for demo, prevents any attempt at XSS
header('Content-Type', 'text/plain');

return $provider;
