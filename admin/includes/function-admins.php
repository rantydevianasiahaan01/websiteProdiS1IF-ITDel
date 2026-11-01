<?php
/**
 * Admin Helper Functions
 * Untuk render komponen UI secara konsisten
 */

/**
 * Render Stats Card
 */
function render_stat_card($title, $number, $icon, $border_color = '#1a2a3a') {
    return '
    <div class="stat-card" style="border-left-color: ' . $border_color . ';">
        <div class="icon"><i class="' . $icon . '"></i></div>
        <h3>' . $title . '</h3>
        <div class="number">' . $number . '</div>
    </div>';
}

/**
 * Render Alert
 */
function render_alert($type, $message, $icon = null) {
    $icons = [
        'success' => 'fas fa-check-circle',
        'danger' => 'fas fa-exclamation-circle',
        'info' => 'fas fa-info-circle',
        'warning' => 'fas fa-exclamation-triangle'
    ];
    $icon = $icon ?? $icons[$type] ?? 'fas fa-info-circle';
    
    return '
    <div class="alert alert-' . $type . '">
        <i class="' . $icon . '"></i>
        <span>' . htmlspecialchars($message) . '</span>
    </div>';
}

/**
 * Render Empty State
 */
function render_empty_state($icon, $message, $button_text = null, $button_url = null) {
    $html = '
    <div class="empty-state">
        <div class="icon"><i class="' . $icon . '"></i></div>
        <p>' . $message . '</p>';
    
    if ($button_text && $button_url) {
        $html .= '<a href="' . $button_url . '" class="btn btn-primary">
            <i class="fas fa-plus"></i> ' . $button_text . '
        </a>';
    }
    
    $html .= '</div>';
    return $html;
}

/**
 * Render Action Buttons
 */
function render_action_buttons($actions) {
    $html = '<div class="action-buttons">';
    foreach ($actions as $action) {
        $html .= '<a href="' . $action['url'] . '" 
                     class="btn-action btn-' . $action['type'] . '" 
                     title="' . $action['title'] . '"';
        if (isset($action['confirm'])) {
            $html .= ' onclick="return confirm(\'' . addslashes($action['confirm']) . '\')"';
        }
        if (isset($action['target'])) {
            $html .= ' target="' . $action['target'] . '"';
        }
        $html .= '>
            <i class="' . $action['icon'] . '"></i>
        </a>';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Render Photo
 */
function render_photo($photo_path, $alt = 'Foto', $size = 50) {
    if (!empty($photo_path) && file_exists(__DIR__ . '/../../assets/uploads/' . $photo_path)) {
        return '<img src="../../assets/uploads/' . htmlspecialchars($photo_path) . '" 
                 class="photo-circle" 
                 style="width: ' . $size . 'px; height: ' . $size . 'px;"
                 alt="' . htmlspecialchars($alt) . '">';
    } else {
        return '<div class="photo-placeholder" style="width: ' . $size . 'px; height: ' . $size . 'px;">
            <i class="fas fa-user"></i>
        </div>';
    }
}

/**
 * Render Badge
 */
function render_badge($text, $type = 'secondary') {
    return '<span class="badge badge-' . $type . '">' . htmlspecialchars($text) . '</span>';
}

/**
 * Render Page Header
 */
function render_page_header($title, $icon, $button_text = null, $button_url = null) {
    $html = '
    <div class="header">
        <h1><i class="' . $icon . '"></i> ' . $title . '</h1>';
    
    if ($button_text && $button_url) {
        $html .= '<a href="' . $button_url . '" class="btn btn-primary">
            <i class="fas fa-plus"></i> ' . $button_text . '
        </a>';
    }
    
    $html .= '</div>';
    return $html;
}

/**
 * Auto display alert from GET/SESSION
 */
function auto_display_alerts() {
    if (isset($_GET['msg'])) {
        echo render_alert('success', $_GET['msg']);
    }
    if (isset($_GET['error'])) {
        echo render_alert('danger', $_GET['error']);
    }
    if (isset($_SESSION['success'])) {
        echo render_alert('success', $_SESSION['success']);
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo render_alert('danger', $_SESSION['error']);
        unset($_SESSION['error']);
    }
}
?>