<?php $user = $_SESSION['user']; $isEdit = !empty($editing); $isLevelEdit = !empty($editingLevel); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Game Library &mdash; Game Management</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="app-body">

<header class="navbar">
    <div class="navbar-inner">
        <a class="brand" href="index.php?page=admin">
            <span class="brand-icon">&#127918;</span>
            <span>GameSys</span>
        </a>
        <div class="nav-user">
            <span class="user-pill">
                <span class="user-avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                <span class="user-meta">
                    <span class="user-name"><?= htmlspecialchars($user['name']) ?></span>
                    <span class="user-role">Admin Panel</span>
                </span>
            </span>
            <a href="index.php?page=logout" class="btn-logout">Sign Out</a>
        </div>
    </div>
</header>

<main class="main-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Admin Game Setup</h1>
            <p class="page-sub">Create game records, organize levels and prepare score tracking</p>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <?php $messages = ['game_added' => 'Game added successfully.',
                           'game_updated' => 'Game updated successfully.',
                           'game_deleted' => 'Game deleted successfully.',
                           'level_added' => 'Level added successfully.',
                           'level_updated' => 'Level updated successfully.',
                           'level_deleted' => 'Level deleted successfully.'];
              $msg = $messages[$_GET['msg']] ?? null; ?>
        <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- ============ Add / Edit Game Form ============ -->
    <div class="card form-card">
        <h3 class="card-title">
            <?= $isEdit ? '&#9998; Edit Game Details (#' . intval($editing['id']) . ')' : '+ Add Game Details' ?>
        </h3>
        <form method="POST"
              action="index.php?page=admin&action=<?= $isEdit ? 'update&id=' . intval($editing['id']) : 'add' ?>"
              class="form" novalidate>
            <div class="field-row">
                <div class="field">
                    <label for="title">Game Name</label>
                    <input type="text" id="title" name="title"
                           value="<?= htmlspecialchars($editing['title'] ?? '') ?>"
                           placeholder="e.g. Adventure Quest" required>
                </div>
                <div class="field">
                    <label for="genre">Game Genre</label>
                    <input type="text" id="genre" name="genre"
                           value="<?= htmlspecialchars($editing['genre'] ?? '') ?>"
                           placeholder="e.g. Adventure" required>
                </div>
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="release_date">Release Date</label>
                    <input type="date" id="release_date" name="release_date"
                           value="<?= htmlspecialchars($editing['release_date'] ?? '') ?>">
                </div>
                <div class="field">
                    <label for="description">Game Description</label>
                    <textarea id="description" name="description"
                              placeholder="Short game description"><?= htmlspecialchars($editing['description'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="form-actions">
                <?php if ($isEdit): ?>
                    <a href="index.php?page=admin" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Game Details</button>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary">Save Game Details</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- ============ Games Table ============ -->
    <div class="card">
        <div class="card-toolbar">
            <div class="search-wrap">
                <span class="search-icon">&#128269;</span>
                <input type="text" id="gameSearchInput" class="search-input"
                       placeholder="Search game name, genre, description or release date...">
            </div>
            <span class="badge" id="gameResultCount"><?= count($games) ?> total</span>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Game Name</th>
                        <th>Game Genre</th>
                        <th>Released On</th>
                        <th>Game Details</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="gameTableBody">
                    <?php if (empty($games)): ?>
                        <tr><td colspan="6" class="empty">No game records yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($games as $i => $game): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($game['title']) ?></td>
                                <td><?= htmlspecialchars($game['genre']) ?></td>
                                <td><?= htmlspecialchars($game['release_date'] ?? 'Not set') ?></td>
                                <td><?= htmlspecialchars($game['description'] ?? '') ?></td>
                                <td class="text-right">
                                    <a class="btn-sm btn-edit"
                                       href="index.php?page=admin&action=edit&id=<?= $game['id'] ?>">Edit</a>
                                    <a class="btn-sm btn-delete"
                                       href="index.php?page=admin&action=delete&id=<?= $game['id'] ?>"
                                       onclick="return confirm('Delete this game record? Related levels and scores will also be removed.')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (!empty($levelError)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($levelError) ?></div>
    <?php endif; ?>

    <!-- ============ Add / Edit Level Form ============ -->
    <div class="card form-card">
        <h3 class="card-title">
            <?= $isLevelEdit ? '&#9998; Edit Level Details (#' . intval($editingLevel['id']) . ')' : '+ Add Level Details' ?>
        </h3>
        <form method="POST"
              action="index.php?page=admin&action=<?= $isLevelEdit ? 'level_update&id=' . intval($editingLevel['id']) : 'level_add' ?>"
              class="form" novalidate>
            <div class="field-row">
                <div class="field">
                    <label for="game_id">Parent Game</label>
                    <select id="game_id" name="game_id" required>
                        <option value="">Choose game</option>
                        <?php foreach ($games as $game): ?>
                            <option value="<?= $game['id'] ?>"
                                <?= intval($editingLevel['game_id'] ?? 0) === intval($game['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($game['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="level_number">Level No.</label>
                    <input type="number" id="level_number" name="level_number" min="1"
                           value="<?= htmlspecialchars($editingLevel['level_number'] ?? '') ?>"
                           placeholder="1" required>
                </div>
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="level_name">Level Title</label>
                    <input type="text" id="level_name" name="level_name"
                           value="<?= htmlspecialchars($editingLevel['level_name'] ?? '') ?>"
                           placeholder="e.g. Beginner Forest" required>
                </div>
                <div class="field">
                    <label for="difficulty">Difficulty Level</label>
                    <select id="difficulty" name="difficulty" required>
                        <?php foreach ($difficulties as $difficulty): ?>
                            <option value="<?= $difficulty ?>"
                                <?= ($editingLevel['difficulty'] ?? 'Easy') === $difficulty ? 'selected' : '' ?>>
                                <?= $difficulty ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <?php if ($isLevelEdit): ?>
                    <a href="index.php?page=admin" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Level Details</button>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary">Save Level Details</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- ============ Levels Table ============ -->
    <div class="card">
        <div class="card-toolbar">
            <div class="search-wrap">
                <span class="search-icon">&#128269;</span>
                <input type="text" id="levelSearchInput" class="search-input"
                       placeholder="Search game, level title or difficulty...">
            </div>
            <span class="badge" id="levelResultCount"><?= count($levels) ?> total</span>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Parent Game</th>
                        <th>Level Title</th>
                        <th>Difficulty Level</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="levelTableBody">
                    <?php if (empty($levels)): ?>
                        <tr><td colspan="5" class="empty">No level records yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($levels as $i => $level): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($level['game_title']) ?></td>
                                <td>Level <?= htmlspecialchars($level['level_number']) ?>: <?= htmlspecialchars($level['level_name']) ?></td>
                                <td><?= htmlspecialchars($level['difficulty']) ?></td>
                                <td class="text-right">
                                    <a class="btn-sm btn-edit"
                                       href="index.php?page=admin&action=level_edit&id=<?= $level['id'] ?>">Edit</a>
                                    <a class="btn-sm btn-delete"
                                       href="index.php?page=admin&action=level_delete&id=<?= $level['id'] ?>"
                                       onclick="return confirm('Delete this level record? Related scores will keep the game but lose the level link.')">Delete</a>
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

<!-- =========== Inline AJAX search =========== -->
<script>
(function () {
    var gameInput    = document.getElementById('gameSearchInput');
    var gameBody     = document.getElementById('gameTableBody');
    var gameCounter  = document.getElementById('gameResultCount');
    var levelInput   = document.getElementById('levelSearchInput');
    var levelBody    = document.getElementById('levelTableBody');
    var levelCounter = document.getElementById('levelResultCount');
    var gameTimer, levelTimer;

    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    }

    function renderGames(rows) {
        if (!rows.length) {
            gameBody.innerHTML = '<tr><td colspan="6" class="empty">No game records found.</td></tr>';
            gameCounter.textContent = '0 results';
            return;
        }
        var html = '';
        rows.forEach(function (g, i) {
            html +=
                '<tr>' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td>' + esc(g.title) + '</td>' +
                    '<td>' + esc(g.genre) + '</td>' +
                    '<td>' + esc(g.release_date || 'Not set') + '</td>' +
                    '<td>' + esc(g.description) + '</td>' +
                    '<td class="text-right">' +
                        '<a class="btn-sm btn-edit" href="index.php?page=admin&action=edit&id=' + g.id + '">Edit</a>' +
                        '<a class="btn-sm btn-delete" href="index.php?page=admin&action=delete&id=' + g.id +
                        '" onclick="return confirm(\'Delete this game record? Related levels and scores will also be removed.\')">Delete</a>' +
                    '</td>' +
                '</tr>';
        });
        gameBody.innerHTML = html;
        gameCounter.textContent = rows.length + (gameInput.value.trim() ? ' results' : ' total');
    }

    function renderLevels(rows) {
        if (!rows.length) {
            levelBody.innerHTML = '<tr><td colspan="5" class="empty">No level records found.</td></tr>';
            levelCounter.textContent = '0 results';
            return;
        }
        var html = '';
        rows.forEach(function (l, i) {
            html +=
                '<tr>' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td>' + esc(l.game_title) + '</td>' +
                    '<td>Level ' + esc(l.level_number) + ': ' + esc(l.level_name) + '</td>' +
                    '<td>' + esc(l.difficulty) + '</td>' +
                    '<td class="text-right">' +
                        '<a class="btn-sm btn-edit" href="index.php?page=admin&action=level_edit&id=' + l.id + '">Edit</a>' +
                        '<a class="btn-sm btn-delete" href="index.php?page=admin&action=level_delete&id=' + l.id +
                        '" onclick="return confirm(\'Delete this level record? Related scores will keep the game but lose the level link.\')">Delete</a>' +
                    '</td>' +
                '</tr>';
        });
        levelBody.innerHTML = html;
        levelCounter.textContent = rows.length + (levelInput.value.trim() ? ' results' : ' total');
    }

    gameInput.addEventListener('input', function () {
        clearTimeout(gameTimer);
        gameTimer = setTimeout(function () {
            fetch('index.php?page=ajax&type=game&q=' + encodeURIComponent(gameInput.value.trim()),
                  { credentials: 'same-origin' })
                .then(function (r) { return r.json(); })
                .then(renderGames)
                .catch(function (e) { console.error(e); });
        }, 200);
    });

    levelInput.addEventListener('input', function () {
        clearTimeout(levelTimer);
        levelTimer = setTimeout(function () {
            fetch('index.php?page=ajax&type=level&q=' + encodeURIComponent(levelInput.value.trim()),
                  { credentials: 'same-origin' })
                .then(function (r) { return r.json(); })
                .then(renderLevels)
                .catch(function (e) { console.error(e); });
        }, 200);
    });
})();
</script>

</body>
</html>
