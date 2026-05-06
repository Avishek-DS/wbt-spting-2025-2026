<?php
// ================================================================
// MODELS - All DB access using procedural mysqli + prepared stmts
// ================================================================

/* ---------------- Password ---------------- */
function verifyStoredPassword($password, $stored) {
    if (password_verify($password, $stored)) {
        return true;
    }
    return hash_equals((string) $stored, (string) $password);
}

/* ------------------- Admin ------------------- */
function authAdmin($conn, $username, $password) {
    $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM admins WHERE username = ?");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return ($row && verifyStoredPassword($password, $row['password'])) ? $row : false;
}

/* ------------------ Player ------------------ */
function authPlayer($conn, $username, $password) {
    $stmt = mysqli_prepare($conn,
        "SELECT id, name, username, password FROM players WHERE username = ?");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return ($row && verifyStoredPassword($password, $row['password'])) ? $row : false;
}

function addPlayer($conn, $name, $contact, $email, $username, $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn,
        "INSERT INTO players (name, contact, email, username, password) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sssss', $name, $contact, $email, $username, $hash);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function playerUsernameExists($conn, $username, $excludeId = null) {
    if ($excludeId) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM players WHERE username = ? AND id != ?");
        mysqli_stmt_bind_param($stmt, 'si', $username, $excludeId);
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id FROM players WHERE username = ?");
        mysqli_stmt_bind_param($stmt, 's', $username);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $exists = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $exists;
}

function playerEmailExists($conn, $email, $excludeId = null) {
    if ($excludeId) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM players WHERE email = ? AND id != ?");
        mysqli_stmt_bind_param($stmt, 'si', $email, $excludeId);
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id FROM players WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $exists = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $exists;
}

/* ------------------- Game ------------------- */
function getGames($conn) {
    $r = mysqli_query($conn, "SELECT id, title, genre, description, release_date FROM games ORDER BY id DESC");
    return $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];
}

function getGame($conn, $id) {
    $stmt = mysqli_prepare($conn, "SELECT id, title, genre, description, release_date FROM games WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row;
}

function gameExists($conn, $id) {
    $stmt = mysqli_prepare($conn, "SELECT id FROM games WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $exists = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $exists;
}

function addGame($conn, $title, $genre, $description, $releaseDate) {
    $releaseDate = $releaseDate === '' ? null : $releaseDate;
    $description = $description === '' ? null : $description;
    $stmt = mysqli_prepare($conn,
        "INSERT INTO games (title, genre, description, release_date) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssss', $title, $genre, $description, $releaseDate);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function updateGame($conn, $id, $title, $genre, $description, $releaseDate) {
    $releaseDate = $releaseDate === '' ? null : $releaseDate;
    $description = $description === '' ? null : $description;
    $stmt = mysqli_prepare($conn,
        "UPDATE games SET title = ?, genre = ?, description = ?, release_date = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'ssssi', $title, $genre, $description, $releaseDate, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function deleteGame($conn, $id) {
    $stmt = mysqli_prepare($conn, "DELETE FROM games WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function searchGames($conn, $term) {
    $like = '%' . $term . '%';
    $stmt = mysqli_prepare($conn,
        "SELECT id, title, genre, description, release_date FROM games
         WHERE title LIKE ? OR genre LIKE ? OR description LIKE ? OR release_date LIKE ?
         ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, 'ssss', $like, $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

/* ---------------- Game Level ---------------- */
function getLevels($conn) {
    $r = mysqli_query($conn,
        "SELECT l.id, l.game_id, l.level_number, l.level_name, l.difficulty, g.title AS game_title
         FROM game_levels l
         JOIN games g ON l.game_id = g.id
         ORDER BY g.title ASC, l.level_number ASC");
    return $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];
}

function getLevel($conn, $id) {
    $stmt = mysqli_prepare($conn,
        "SELECT l.id, l.game_id, l.level_number, l.level_name, l.difficulty, g.title AS game_title
         FROM game_levels l
         JOIN games g ON l.game_id = g.id
         WHERE l.id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row;
}

function levelBelongsToGame($conn, $levelId, $gameId) {
    $stmt = mysqli_prepare($conn, "SELECT id FROM game_levels WHERE id = ? AND game_id = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $levelId, $gameId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $exists = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $exists;
}

function addLevel($conn, $gameId, $levelNumber, $levelName, $difficulty) {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO game_levels (game_id, level_number, level_name, difficulty) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'iiss', $gameId, $levelNumber, $levelName, $difficulty);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function updateLevel($conn, $id, $gameId, $levelNumber, $levelName, $difficulty) {
    $stmt = mysqli_prepare($conn,
        "UPDATE game_levels SET game_id = ?, level_number = ?, level_name = ?, difficulty = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'iissi', $gameId, $levelNumber, $levelName, $difficulty, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function deleteLevel($conn, $id) {
    $stmt = mysqli_prepare($conn, "DELETE FROM game_levels WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function searchLevels($conn, $term) {
    $like = '%' . $term . '%';
    $stmt = mysqli_prepare($conn,
        "SELECT l.id, l.game_id, l.level_number, l.level_name, l.difficulty, g.title AS game_title
         FROM game_levels l
         JOIN games g ON l.game_id = g.id
         WHERE g.title LIKE ? OR l.level_name LIKE ? OR l.difficulty LIKE ? OR l.level_number LIKE ?
         ORDER BY g.title ASC, l.level_number ASC");
    mysqli_stmt_bind_param($stmt, 'ssss', $like, $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

/* ---------------- Player Score ---------------- */
function getPlayerScores($conn, $playerId) {
    $stmt = mysqli_prepare($conn,
        "SELECT s.id, s.player_id, s.game_id, s.level_id, s.score, s.time_taken, s.player_rank, s.played_at,
                g.title AS game_title, l.level_number, l.level_name, l.difficulty
         FROM player_scores s
         JOIN games g ON s.game_id = g.id
         LEFT JOIN game_levels l ON s.level_id = l.id
         WHERE s.player_id = ?
         ORDER BY s.played_at DESC, s.id DESC");
    mysqli_stmt_bind_param($stmt, 'i', $playerId);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

function getPlayerScore($conn, $id, $playerId) {
    $stmt = mysqli_prepare($conn,
        "SELECT s.id, s.player_id, s.game_id, s.level_id, s.score, s.time_taken, s.player_rank, s.played_at,
                g.title AS game_title, l.level_number, l.level_name, l.difficulty
         FROM player_scores s
         JOIN games g ON s.game_id = g.id
         LEFT JOIN game_levels l ON s.level_id = l.id
         WHERE s.id = ? AND s.player_id = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $id, $playerId);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row;
}

function addPlayerScore($conn, $playerId, $gameId, $levelId, $score, $timeTaken, $playerRank) {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO player_scores (player_id, game_id, level_id, score, time_taken, player_rank)
         VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'iiiiid', $playerId, $gameId, $levelId, $score, $timeTaken, $playerRank);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function updatePlayerScore($conn, $id, $playerId, $gameId, $levelId, $score, $timeTaken, $playerRank) {
    $stmt = mysqli_prepare($conn,
        "UPDATE player_scores SET game_id = ?, level_id = ?, score = ?, time_taken = ?, player_rank = ?
         WHERE id = ? AND player_id = ?");
    mysqli_stmt_bind_param($stmt, 'iiiidii', $gameId, $levelId, $score, $timeTaken, $playerRank, $id, $playerId);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function deletePlayerScore($conn, $id, $playerId) {
    $stmt = mysqli_prepare($conn, "DELETE FROM player_scores WHERE id = ? AND player_id = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $id, $playerId);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function searchPlayerScores($conn, $playerId, $term) {
    $like = '%' . $term . '%';
    $stmt = mysqli_prepare($conn,
        "SELECT s.id, s.player_id, s.game_id, s.level_id, s.score, s.time_taken, s.player_rank, s.played_at,
                g.title AS game_title, l.level_number, l.level_name, l.difficulty
         FROM player_scores s
         JOIN games g ON s.game_id = g.id
         LEFT JOIN game_levels l ON s.level_id = l.id
         WHERE s.player_id = ?
           AND (g.title LIKE ? OR l.level_name LIKE ? OR l.difficulty LIKE ? OR s.score LIKE ?)
         ORDER BY s.played_at DESC, s.id DESC");
    mysqli_stmt_bind_param($stmt, 'issss', $playerId, $like, $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}
?>
