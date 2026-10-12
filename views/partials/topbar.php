<header class="topbar">
    <?php if (isset($topbarLinks) && is_array($topbarLinks)): ?>
        <nav class="topbar-nav" aria-label="Section navigation">
            <?php foreach ($topbarLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['url']); ?>"
                   class="topbar-tab <?php echo !empty($link['active']) ? 'tab-active' : ''; ?>"
                   <?php echo !empty($link['active']) ? 'aria-current="page"' : ''; ?>>
                    <span class="tab-text"><?php echo htmlspecialchars($link['label']); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php else: ?>
        <div class="topbar-title">Overview</div>
    <?php endif; ?>
</header>

<?php require_once __DIR__ . '/flash.php'; ?>

<main class="content-area">