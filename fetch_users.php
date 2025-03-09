<?php
require 'api_calls.php';
session_start();

if (!isset($_SESSION['token'])) {
    die("Unauthorized access.");
}

$token = $_SESSION['token'];

if (isset($_GET['event_id'])) {
    $event_id = intval($_GET['event_id']);

    $registered = fetchUsersByEvent($event_id, $token);
    if (!empty($registered)) {
        echo "<div class='profile-wrapper'>";
        foreach ($registered as $user) {
            echo "<div class='profile-container'>";
            echo "<a href='https://profile.intra.42.fr/users/{$user['user']['login']}' target='_blank' rel='noopener'>
            <div class='image-cropper'>
            <img class='img-profile' src='{$user['user']['image']['versions']['small']}' alt='{$user['user']['login']}' />
            </div>
            <span class='profile-name'>{$user['user']['login']}</span></a>
            </div>";
        }
        echo "</div>";
    } else {
        echo "<strong>No users found.</strong>";
    }
}
?>