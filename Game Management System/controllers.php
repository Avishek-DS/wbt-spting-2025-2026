<?php

function isValidDate($date) {
    if ($date === '') return true;
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function loginCtrl($conn) {
    $error = '';
    $prefill = $_COOKIE['remember_user'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $u = trim($_POST['username'] ?? '');
        $p = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        if ($u === '' || $p === '') {
            $error = 'Please fill in both fields.';
        } else {
              $admin = authAdmin($conn, $u, $p);
            if ($admin) {
                $_SESSION['user'] = [
                    'id' => $admin['id'], 'username' => $admin['username'],
                    'name' => 'Administrator', 'role' => 'admin'
                ];
                if ($remember) setcookie('remember_user', $u, time() + 86400 * 30, '/');
                else setcookie('remember_user', '', time() - 3600, '/');
                header('Location: index.php?page=admin');
                exit;
            }
            $player = authPlayer($conn, $u, $p);
            if ($player) {
                $_SESSION['user'] = [
                    'id' => $player['id'], 'username' => $player['username'],
                    'name' => $player['name'], 'role' => 'player'
                ];
                if ($remember) setcookie('remember_user', $u, time() + 86400 * 30, '/');
                else setcookie('remember_user', '', time() - 3600, '/');
                header('Location: index.php?page=player');
                exit;
            }
            $error = 'Invalid username or password.';
        }
    }

    require 'views/login.php';
}

function registerCtrl($conn) {
    $error = $success = '';
    $old = ['name' => '', 'contact' => '', 'email' => '', 'username' => ''];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name     = trim($_POST['name'] ?? '');
        $contact  = trim($_POST['contact'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $old = compact('name', 'contact', 'email', 'username');

        if ($name === '' || $contact === '' || $email === '' || $username === '' || $password === '') {
            $error = 'All fields are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } elseif (playerUsernameExists($conn, $username)) {
            $error = 'Username is already taken.';
        } elseif (playerEmailExists($conn, $email)) {
            $error = 'Email is already registered.';
        } else {
            if (addPlayer($conn, $name, $contact, $email, $username, $password)) {
                $success = 'Account created! You can now log in.';
                $old = ['name' => '', 'contact' => '', 'email' => '', 'username' => ''];
            } else {
                $error = 'Registration failed. Try again.';
            }
        }
    }

    require 'views/register.php';
}

function adminCtrl($conn) {
    $action = $_GET['action'] ?? 'list';
    $error = '';
    $levelError = '';
    $editing = null;       
    $editingLevel = null; 
    $difficulties = ['Easy', 'Medium', 'Hard'];

   
    if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $title       = trim($_POST['title'] ?? '');
        $genre       = trim($_POST['genre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $releaseDate = trim($_POST['release_date'] ?? '');

        if ($title === '' || $genre === '') {
            $error = 'Title and genre are required.';
        } elseif (!isValidDate($releaseDate)) {
            $error = 'Release date must be a valid date.';
        } else {
            if (addGame($conn, $title, $genre, $description, $releaseDate)) {
                header('Location: index.php?page=admin&msg=game_added');
                exit;
            }
            $error = 'Failed to add game.';
        }
    }

    if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id          = intval($_GET['id'] ?? 0);
        $title       = trim($_POST['title'] ?? '');
        $genre       = trim($_POST['genre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $releaseDate = trim($_POST['release_date'] ?? '');

      
        if ($title === '' || $genre === '') {
            $error = 'Title and genre cannot be empty (NULL).';
            $editing = ['id' => $id, 'title' => $title, 'genre' => $genre,
                        'description' => $description, 'release_date' => $releaseDate];
        } elseif (!isValidDate($releaseDate)) {
            $error = 'Release date must be a valid date.';
            $editing = ['id' => $id, 'title' => $title, 'genre' => $genre,
                        'description' => $description, 'release_date' => $releaseDate];
        } else {
            if (updateGame($conn, $id, $title, $genre, $description, $releaseDate)) {
                header('Location: index.php?page=admin&msg=game_updated');
                exit;
            }
            $error = 'Update failed.';
            $editing = ['id' => $id, 'title' => $title, 'genre' => $genre,
                        'description' => $description, 'release_date' => $releaseDate];
        }
    }

    /* --- Show game edit form (GET) --- */
    if ($action === 'edit' && !$editing) {
        $id = intval($_GET['id'] ?? 0);
        $editing = getGame($conn, $id);
        if (!$editing) $error = 'Game not found.';
    }

    /* --- Delete game (GET) --- */
    if ($action === 'delete') {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) deleteGame($conn, $id);
        header('Location: index.php?page=admin&msg=game_deleted');
        exit;
    }

    /* --- Add level (POST) --- */
    if ($action === 'level_add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $gameId      = intval($_POST['game_id'] ?? 0);
        $levelNumber = trim($_POST['level_number'] ?? '');
        $levelName   = trim($_POST['level_name'] ?? '');
        $difficulty  = trim($_POST['difficulty'] ?? '');

        if ($gameId <= 0 || !gameExists($conn, $gameId)) {
            $levelError = 'Please choose a valid game.';
        } elseif ($levelNumber === '' || !ctype_digit($levelNumber) || intval($levelNumber) <= 0) {
            $levelError = 'Level number must be a positive whole number.';
        } elseif ($levelName === '') {
            $levelError = 'Level name is required.';
        } elseif (!in_array($difficulty, $difficulties, true)) {
            $levelError = 'Please choose a valid difficulty.';
        } else {
            if (addLevel($conn, $gameId, intval($levelNumber), $levelName, $difficulty)) {
                header('Location: index.php?page=admin&msg=level_added');
                exit;
            }
            $levelError = 'Failed to add level.';
        }
    }

    /* --- Update level (POST) --- */
    if ($action === 'level_update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id          = intval($_GET['id'] ?? 0);
        $gameId      = intval($_POST['game_id'] ?? 0);
        $levelNumber = trim($_POST['level_number'] ?? '');
        $levelName   = trim($_POST['level_name'] ?? '');
        $difficulty  = trim($_POST['difficulty'] ?? '');

        // ===== NULL VALIDATION on UPDATE =====
        if ($gameId <= 0 || !gameExists($conn, $gameId)) {
            $levelError = 'Please choose a valid game.';
            $editingLevel = ['id' => $id, 'game_id' => $gameId, 'level_number' => $levelNumber,
                             'level_name' => $levelName, 'difficulty' => $difficulty];
        } elseif ($levelNumber === '' || !ctype_digit($levelNumber) || intval($levelNumber) <= 0) {
            $levelError = 'Level number must be a positive whole number.';
            $editingLevel = ['id' => $id, 'game_id' => $gameId, 'level_number' => $levelNumber,
                             'level_name' => $levelName, 'difficulty' => $difficulty];
        } elseif ($levelName === '') {
            $levelError = 'Level name cannot be empty (NULL).';
            $editingLevel = ['id' => $id, 'game_id' => $gameId, 'level_number' => $levelNumber,
                             'level_name' => $levelName, 'difficulty' => $difficulty];
        } elseif (!in_array($difficulty, $difficulties, true)) {
            $levelError = 'Please choose a valid difficulty.';
            $editingLevel = ['id' => $id, 'game_id' => $gameId, 'level_number' => $levelNumber,
                             'level_name' => $levelName, 'difficulty' => $difficulty];
        } else {
            if (updateLevel($conn, $id, $gameId, intval($levelNumber), $levelName, $difficulty)) {
                header('Location: index.php?page=admin&msg=level_updated');
                exit;
            }
            $levelError = 'Update failed.';
            $editingLevel = ['id' => $id, 'game_id' => $gameId, 'level_number' => $levelNumber,
                             'level_name' => $levelName, 'difficulty' => $difficulty];
        }
    }
    if ($action === 'level_edit' && !$editingLevel) {
        $id = intval($_GET['id'] ?? 0);
        $editingLevel = getLevel($conn, $id);
        if (!$editingLevel) $levelError = 'Level not found.';
    }

    if ($action === 'level_delete') {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) deleteLevel($conn, $id);
        header('Location: index.php?page=admin&msg=level_deleted');
        exit;
    }

    $games = getGames($conn);
    $levels = getLevels($conn);
    require 'views/admin.php';
}

function playerCtrl($conn) {
    $action = $_GET['action'] ?? 'list';
    $error = '';
    $editing = null;
    $playerId = $_SESSION['user']['id'];

    /* --- Add (POST) --- */
    if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $gameId     = intval($_POST['game_id'] ?? 0);
        $levelRaw   = trim($_POST['level_id'] ?? '');
        $levelId    = $levelRaw === '' ? null : intval($levelRaw);
        $score      = trim($_POST['score'] ?? '');
        $timeTaken  = trim($_POST['time_taken'] ?? '');
        $playerRank = trim($_POST['player_rank'] ?? '');

        if ($gameId <= 0 || !gameExists($conn, $gameId)) {
            $error = 'Please choose a valid game.';
        } elseif ($levelId !== null && !levelBelongsToGame($conn, $levelId, $gameId)) {
            $error = 'Selected level does not belong to this game.';
        } elseif ($score === '' || !ctype_digit($score)) {
            $error = 'Score must be a non-negative whole number.';
        } elseif ($timeTaken === '' || !ctype_digit($timeTaken)) {
            $error = 'Time taken must be a non-negative whole number.';
        } elseif ($playerRank === '' || !is_numeric($playerRank) || floatval($playerRank) < 0) {
            $error = 'Rank must be a non-negative number.';
        } else {
            if (addPlayerScore($conn, $playerId, $gameId, $levelId, intval($score), intval($timeTaken), floatval($playerRank))) {
                header('Location: index.php?page=player&msg=added');
                exit;
            }
            $error = 'Failed to add score.';
        }
    }

    /* --- Update (POST) --- */
    if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id         = intval($_GET['id'] ?? 0);
        $gameId     = intval($_POST['game_id'] ?? 0);
        $levelRaw   = trim($_POST['level_id'] ?? '');
        $levelId    = $levelRaw === '' ? null : intval($levelRaw);
        $score      = trim($_POST['score'] ?? '');
        $timeTaken  = trim($_POST['time_taken'] ?? '');
        $playerRank = trim($_POST['player_rank'] ?? '');

        // ===== NULL VALIDATION on UPDATE =====
        if ($gameId <= 0 || !gameExists($conn, $gameId)) {
            $error = 'Please choose a valid game.';
            $editing = ['id' => $id, 'game_id' => $gameId, 'level_id' => $levelId,
                        'score' => $score, 'time_taken' => $timeTaken, 'player_rank' => $playerRank];
        } elseif ($levelId !== null && !levelBelongsToGame($conn, $levelId, $gameId)) {
            $error = 'Selected level does not belong to this game.';
            $editing = ['id' => $id, 'game_id' => $gameId, 'level_id' => $levelId,
                        'score' => $score, 'time_taken' => $timeTaken, 'player_rank' => $playerRank];
        } elseif ($score === '' || !ctype_digit($score)) {
            $error = 'Score must be a non-negative whole number.';
            $editing = ['id' => $id, 'game_id' => $gameId, 'level_id' => $levelId,
                        'score' => $score, 'time_taken' => $timeTaken, 'player_rank' => $playerRank];
        } elseif ($timeTaken === '' || !ctype_digit($timeTaken)) {
            $error = 'Time taken must be a non-negative whole number.';
            $editing = ['id' => $id, 'game_id' => $gameId, 'level_id' => $levelId,
                        'score' => $score, 'time_taken' => $timeTaken, 'player_rank' => $playerRank];
        } elseif ($playerRank === '' || !is_numeric($playerRank) || floatval($playerRank) < 0) {
            $error = 'Rank must be a non-negative number.';
            $editing = ['id' => $id, 'game_id' => $gameId, 'level_id' => $levelId,
                        'score' => $score, 'time_taken' => $timeTaken, 'player_rank' => $playerRank];
        } else {
            if (updatePlayerScore($conn, $id, $playerId, $gameId, $levelId, intval($score), intval($timeTaken), floatval($playerRank))) {
                header('Location: index.php?page=player&msg=updated');
                exit;
            }
            $error = 'Update failed.';
            $editing = ['id' => $id, 'game_id' => $gameId, 'level_id' => $levelId,
                        'score' => $score, 'time_taken' => $timeTaken, 'player_rank' => $playerRank];
        }
    }

    /* --- Show edit form --- */
    if ($action === 'edit' && !$editing) {
        $id = intval($_GET['id'] ?? 0);
        $editing = getPlayerScore($conn, $id, $playerId);
        if (!$editing) $error = 'Score not found.';
    }

    /* --- Delete --- */
    if ($action === 'delete') {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) deletePlayerScore($conn, $id, $playerId);
        header('Location: index.php?page=player&msg=deleted');
        exit;
    }

    $games = getGames($conn);
    $levels = getLevels($conn);
    $scores = getPlayerScores($conn, $playerId);
    require 'views/player.php';
}
?>
