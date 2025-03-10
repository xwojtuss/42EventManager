<?php
require 'api_calls.php';
session_start();

if (!isset($_SESSION['access_token'])) {
    die("Unauthorized access.");
}

$token = $_SESSION['access_token'];

if (isset($_GET['event_id'])) {
    $event_id = intval($_GET['event_id']);

    $feedbacks = fetchFeedbacksByEvent($event_id, $token);
    if (!empty($feedbacks)) {
        foreach ($feedbacks as $feedback) {
            echo "<div class='review'>";
            echo "<span class='feedback-owner'>
                    <a href='https://profile.intra.42.fr/users/{$feedback['user']['login']}' target='_blank' rel='noopener'>
                    {$feedback['user']['login']}
                    </a>'s review:</span>";
            echo "<span class='feedback-content'>{$feedback['comment']}</span>";
            foreach ($feedback['feedback_details'] as $rating) {
                echo "<div class='rating-item'>";
                echo "<span class='feedback-kind'>{$rating['kind']}</span>";
                echo "<span class='feedback-rate'>";
                for ($i = 1; $i <= $rating['rate']; $i++) {
                    echo "&#9733; ";
                }
                for ($i = $rating['rate'] + 1; $i <= 4; $i++) {
                    echo "&#9734; ";
                }
                echo "</span>";
                echo "</div>";
            }
            echo "</div>";
        }
    } else {
        echo "<strong>No feedbacks found.</strong>";
    }
}
?>