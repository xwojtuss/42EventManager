<?php

$env = parse_ini_file('.env');

if (isset($_GET['error'])) {
    header("Location: {$env['INDEX_URL']}", TRUE, 301);
    exit();
}
// else if (!isset($_GET['code']))
//     die('Unauthorized access');

require 'api_calls.php';
session_start();

if (!isset($_SESSION['access_token']) || !isset($_SESSION['expires_at']) || $_SESSION['expires_at'] <= time())
    getToken($_GET['code']);
$campuses = fetchCampuses($_SESSION['access_token']);
$selected_id = $_GET['campus'] ?? null;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 30;
$events = $selected_id ? fetchEventsByCampus($selected_id, $_SESSION['access_token'], $page, $per_page) : [];
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="loader.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>42Event-Manager</title>
</head>
<body>
<h1>Select Your Campus</h1>

<form method="GET">
    <label for="campuses">Choose a campus:</label>
    <select id="campuses" name="campus" class="campus-select">
        <?php foreach ($campuses as $campus_data): ?>
            <option value="<?= htmlspecialchars($campus_data['id']) ?>" <?= $selected_id == $campus_data['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($campus_data['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="submit-btn">Submit</button>
</form>

<?php if ($selected_id): ?>
    <h2>Events at the selected campus</h2>
    <?php if (!empty($events)): ?>
            
    <?php foreach ($events as $event): ?>
        <div class="parent event-type-<?= htmlspecialchars($event['kind']) ?>">
            <div class="date">
                <div class="day"><?= getdate(strtotime(htmlspecialchars($event['begin_at'])))["mday"] ?></div>
                <div class="month"><?= date("F", strtotime(htmlspecialchars($event['begin_at']))) ?></div>
                <div class="year"><?= getdate(strtotime(htmlspecialchars($event['begin_at'])))["year"] ?></div>
            </div>
            <div class="event-info grandchild-wrapper">
                <div class="container">
                    <div class="left">
                        <p class="event-type"><?= htmlspecialchars($event['kind']) ?></p>
                        <p class="event-title"><?= htmlspecialchars($event['name']) ?></p>
                        <div class="event-duration">duration: 
                        <?php
                            $duration = (strtotime(htmlspecialchars($event['end_at'])) - strtotime(htmlspecialchars($event['begin_at']))) / 3600;
                            if ($duration > 1)
                                echo "$duration hours";
                            else
                                echo "$duration hour";
                        ?>
                        </div>
                        <div class="event-location">location: <?= htmlspecialchars($event['location']) ?></div>
                    </div>
                    <div class="right">
                        <button class="show-users-btn" data-id="<?= htmlspecialchars($event['id']) ?>">
                            <img src="users.svg" class="img-users" alt="registered-users" />
                        </button>
                        <br />
                        <button class="show-feedback-btn" data-id="<?= htmlspecialchars($event['id']) ?>">
                            <img src="feedback.svg" class="img-feedback" alt="feedbacks" />
                        </button>
                    </div>
                </div>
                <div class="event-description" id="desc-<?= htmlspecialchars($event['id']) ?>">
                    <hr />
                    <span class="section-title">Description:</span>
                    <span><?= nl2br(htmlspecialchars($event['description'])) ?></span>
                </div>
                <button class="expand-bar toggle-desc" data-id="<?= htmlspecialchars($event['id']) ?>">
                    <img class="triangle" src="polygon.svg" alt="slider-triangle" />
                </button>
                <div id="userModal-<?= htmlspecialchars($event['id']) ?>" class="modal">
                    <div class="modal-content">
                        <span class="close-modal">&times;</span>
                        <h2>Registered Users</h2>
                        <div class="lds-roller loading-indicator" style="display: none;"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                        <div class="user-list">
                        </div>
                    </div>
                </div>
                <div id="feedbackModal-<?= htmlspecialchars($event['id']) ?>" class="modal">
                    <div class="modal-content">
                        <span class="close-modal">&times;</span>
                        <h2>Registered Feedbacks</h2>
                        <div class="lds-roller loading-indicator" style="display: none;"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                        <div class="feedback-list">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No events found for this campus.</p>
<?php endif; ?>
</div>
    <div class="pagination">
        <?php 
        $query_params = $_GET;
        $query_params['page'] = $page - 1;
        
        if ($page > 1): ?>
            <a href="?<?= http_build_query($query_params) ?>">Previous</a>
        <?php endif; ?>

        <span>Page <?= $page ?></span>

        <?php
        $query_params['page'] = $page + 1;

        if (count($events) === $per_page): ?>
            <a href="?<?= http_build_query($query_params) ?>">Next</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
    <script src="event_listeners.js"></script>
</body>
</html>
