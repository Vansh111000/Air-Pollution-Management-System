<?php
/**
 * Sidebar Component
 * Vertical navigation menu with page links
 */

$menu_items = [
    [
        'label' => 'Dashboard',
        'page' => 'dashboard',
        'icon' => '<svg viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="8" height="8"/><rect x="13" y="3" width="8" height="8"/><rect x="3" y="13" width="8" height="8"/><rect x="13" y="13" width="8" height="8"/></svg>'
    ],
    [
        'label' => 'Sensors',
        'page' => 'sensors',
        'icon' => '<svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="3"/><path d="M12 1C5.9 1 1 5.9 1 12s4.9 11 11 11 11-4.9 11-11S18.1 1 12 1zm0 18c-3.9 0-7-3.1-7-7s3.1-7 7-7 7 3.1 7 7-3.1 7-7 7z"/></svg>'
    ],
    [
        'label' => 'Health',
        'page' => 'health',
        'icon' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3-8h-2v2h-2v-2H9v-2h2V8h2v2h2v2z"/></svg>'
    ]
];

$current_page = $_GET['page'] ?? 'dashboard';
?>

<aside class="sidebar">
    <nav class="sidebar-nav">
        <?php foreach($menu_items as $item): ?>
            <a 
                href="?page=<?php echo $item['page']; ?>" 
                class="nav-item <?php echo $current_page === $item['page'] ? 'active' : ''; ?>"
                data-page="<?php echo $item['page']; ?>"
            >
                <span class="nav-icon">
                    <?php echo $item['icon']; ?>
                </span>
                <span class="nav-label"><?php echo $item['label']; ?></span>
                <?php if($current_page === $item['page']): ?>
                    <span class="nav-indicator"></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>

<style>
/* Sidebar Styles - Will be merged with main stylesheet */
</style>
