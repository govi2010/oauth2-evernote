<?php

$provider = require __DIR__ . '/provider.php';

if (!empty($_GET['error'])) {

    // Got an error, probably user denied access
    exit('Got error: ' . htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));

} elseif (empty($_GET['oauth_token'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: ' . $authUrl);
    exit;

} elseif (empty($_GET['oauth_verifier'])) {
    // State is invalid, possible CSRF attack in progress
    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {
    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken();
    $_SESSION['token'] = serialize($token);

    // Optional: Now you have a token you can look up a users profile data
    header('Location: ./user.php');
}
