<?php

function debugGetToken() {
    $url = "https://api.intra.42.fr/";

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $uid = "UID";
    $secret = "SECRET";
    $token_url = $url . "oauth/token";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        "grant_type" => "client_credentials",
        "client_id" => $uid,
        "client_secret" => $secret,
    ]));

    $response = curl_exec($ch);
    if (!$response) {
        die("cURL error: " . curl_error($ch));
    }
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);

    if ($http_code !== 200 || !isset($data['access_token'])) {
        die("Error getting token: " . json_encode($data));
    }

    $access_token = $data['access_token'];
    $expires_at = time() + $data['expires_in'];
    return $access_token;
}

function fetchCampuses($access_token) {
    $api_url = "https://api.intra.42.fr/v2/campus?per_page=100&sort=name";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $access_token",
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        die("Error fetching API data: " . json_encode(json_decode($response, true)));
    }

    return json_decode($response, true);
}

function fetchEventsByCampus($campus_id, $access_token, $page, $per_page) {
    $events_url = "https://api.intra.42.fr/v2/campus/{$campus_id}/events?page={$page}&per_page={$per_page}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $events_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $access_token",
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        die("Error fetching API data: " . json_encode(json_decode($response, true)));
    }

    return json_decode($response, true);
}

function fetchUsersByEvent($event_id, $access_token) {
    $registered_url = $url . "https://api.intra.42.fr/v2/events/{$event_id}/events_users?per_page=100";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $registered_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $access_token",
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        die("Error fetching API data: " . json_encode(json_decode($response, true)));
    }

    return json_decode($response, true);
}

function fetchFeedbacksByEvent($event_id, $access_token) {
    $feedback_url = $url . "https://api.intra.42.fr/v2/events/{$event_id}/feedbacks?per_page=100";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $feedback_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $access_token",
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        die("Error fetching API data: " . json_encode(json_decode($response, true)));
    }

    return json_decode($response, true);
}
?>
