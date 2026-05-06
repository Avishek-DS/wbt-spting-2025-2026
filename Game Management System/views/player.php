<?php $user = $_SESSION['user']; $isEdit = !empty($editing); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Player Dashboard &mdash; Game Management</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="app-body">

<header class="navbar">
    <div class="navbar-inner">
        <a class="brand" href="index.php?page=player">
            <span class="brand-icon">&#127918;</span>
            <span>GameSys</span>
        </a>
        <div class="nav-user">
            <span class="user-pill">
                <span class="user-avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                <span class="user-meta">
                    <span class="user-name"><?= htmlspecialchars($user['name']) ?></span>
                    <span class="user-role">Player</span>
                </span>
            </span>
            <a href="index.php?page=logout" class="btn-logout">Logout</a>
        </div>
    </div>
</header>

<main class="main-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Manage Scores</h1>
            <p class="page-sub">Add, edit, search and remove your game score records</p>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <?php $messages = ['added' => 'Score added successfully.',
                           'updated' => 'Score updated successfully.',
                           'deleted' => 'Score deleted successfully.'];
              $msg = $messages[$_GET['msg']] ?? null; ?>
        <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card form-card">
        <h3 class="card-title">
            <?= $isEdit ? '&#9998; Edit Score (#' . intval($editing['id']) . ')' : '+ Add New Score' ?>
        </h3>
        <form method="POST"
              action="index.php?page=player&action=<?= $isEdit ? 'update&id=' . intval($editing['id']) : 'add' ?>"
              class="form" novalidate>
            <div class="field-row">
                <div class="field">
                    <label for="game_id">Game</label>
                    <select id="game_id" name="game_id" required>
                        <option value="">Choose game</option>
                        <?php foreach ($games as $game): ?>
                            <option value="<?= $game['id'] ?>"
                                <?= intval($editing['game_id'] ?? 0) === intval($game['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($game['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="level_id">Level</label>
                    <select id="level_id" name="level_id">
                        <option value="">No level</option>
                        <?php foreach ($levels as $level): ?>
                            <option value="<?= $level['id'] ?>" data-game="<?= $level['game_id'] ?>"
                                <?= intval($editing['level_id'] ?? 0) === intval($level['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($level['game_title']) ?> - Level <?= htmlspecialchars($level['level_number']) ?>: <?= htmlspecialchars($level['level_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="score">Score</label>
                    <input type="number" id="score" name="score" min="0"
                           value="<?= htmlspecialchars($editing['score'] ?? '') ?>"
                           placeholder="0" required>
                </div>
                <div class="field">
                    <label for="time_taken">Time Taken (seconds)</label>
                    <input type="number" id="time_taken" name="time_taken" min="0"
                           value="<?= htmlspecialchars($editing['time_taken'] ?? '') ?>"
                           placeholder="0" required>
                </div>
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="player_rank">Rank</label>
                    <input type="number" id="player_rank" name="player_rank" step="0.01" min="0"
                           value="<?= htmlspecialchars($editing['player_rank'] ?? '') ?>"
                           placeholder="0.00" required>
                </div>
            </div>
            <div class="form-actions">
                <?php if ($isEdit): ?>
                    <a href="index.php?page=player" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Score</button>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary">Save Score</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="card-toolbar">
            <div class="search-wrap">
                <span class="search-icon">&#128269;</span>
                <input type="text" id="searchInput" class="search-input"
                       placeholder="Search by game, level, difficulty or score...">
            </div>
            <span class="badge" id="resultCount"><?= count($scores) ?> total</span>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Game</th>
                        <th>Level</th>
                        <th>Difficulty</th>
                        <th>Score</th>
                        <th>Time</th>
                        <th>Rank</th>
                        <th>Played At</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if (empty($scores)): ?>
                        <tr><td colspan="9" class="empty">No scores yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($scores as $i => $score): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($score['game_title']) ?></td>
                                <td>
                                    <?= $score['level_name']
                                        ? 'Level ' . htmlspecialchars($score['level_number']) . ': ' . htmlspecialchars($score['level_name'])
                                        : 'No level' ?>
                                </td>
                                <td><?= htmlspecialchars($score['difficulty'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($score['score']) ?></td>
                                <td><?= htmlspecialchars($score['time_taken']) ?> sec</td>
                                <td><?= number_format($score['player_rank'], 2) ?></td>
                                <td><?= htmlspecialchars($score['played_at']) ?></td>
                                <td class="text-right">
                                    <a class="btn-sm btn-edit"
                                       href="index.php?page=player&action=edit&id=<?= $score['id'] ?>">Edit</a>
                                    <a class="btn-sm btn-delete"
                                       href="index.php?page=player&action=delete&id=<?= $score['id'] ?>"
                                       onclick="return confirm('Delete this score?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer class="footer">&copy; <?= date('Y') ?> Game Management System</footer>

<script>
(function () {
    var input       = document.getElementById('searchInput');
    var body        = document.getElementById('tableBody');
    var counter     = document.getElementById('resultCount');
    var gameSelect  = document.getElementById('game_id');
    var levelSelect = document.getElementById('level_id');
    var timer;

    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    }

    function levelText(row) {
        return row.level_name ? 'Level ' + esc(row.level_number) + ': ' + esc(row.level_name) : 'No level';
    }

    function rankText(value) {
        var n = parseFloat(value || 0);
        return n.toFixed(2);
    }

    function render(rows) {
        if (!rows.length) {
            body.innerHTML = '<tr><td colspan="9" class="empty">No matching results.</td></tr>';
            counter.textContent = '0 results';
            return;
        }
        var html = '';
        rows.forEach(function (s, i) {
            html +=
                '<tr>' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td>' + esc(s.game_title) + '</td>' +
                    '<td>' + levelText(s) + '</td>' +
                    '<td>' + esc(s.difficulty || '-') + '</td>' +
                    '<td>' + esc(s.score) + '</td>' +
                    '<td>' + esc(s.time_taken) + ' sec</td>' +
                    '<td>' + rankText(s.player_rank) + '</td>' +
                    '<td>' + esc(s.played_at) + '</td>' +
                    '<td class="text-right">' +
                        '<a class="btn-sm btn-edit" href="index.php?page=player&action=edit&id=' + s.id + '">Edit</a>' +
                        '<a class="btn-sm btn-delete" href="index.php?page=player&action=delete&id=' + s.id +
                        '" onclick="return confirm(\'Delete this score?\')">Delete</a>' +
                    '</td>' +
                '</tr>';
        });
        body.innerHTML = html;
        counter.textContent = rows.length + (input.value.trim() ? ' results' : ' total');
    }

    function filterLevels() {
        var selectedGame = gameSelect.value;
        Array.prototype.forEach.call(levelSelect.options, function (option) {
            if (option.value === '') {
                option.hidden = false;
                return;
            }
            var show = option.getAttribute('data-game') === selectedGame;
            option.hidden = !show;
            if (!show && option.selected) levelSelect.value = '';
        });
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            fetch('index.php?page=ajax&type=score&q=' + encodeURIComponent(input.value.trim()),
                  { credentials: 'same-origin' })
                .then(function (r) { return r.json(); })
                .then(render)
                .catch(function (e) { console.error(e); });
        }, 200);
    });

    gameSelect.addEventListener('change', filterLevels);
    filterLevels();
})();
</script>

</body>
</html>
