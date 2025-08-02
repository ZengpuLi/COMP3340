<?php
/**
 * Admin Theme Settings
 * Used Car Purchase Website - Admin Panel Theme Management
 */

// Include admin session management
require_once '../../php/admin-session.php';
require_once '../../php/config.php';

// Require admin access
requireAdmin();

// Initialize variables
$error_message = '';
$success_message = '';
$current_theme = getSiteSetting('default_theme', 'default');

// Available themes
$themes = [
    'default' => [
        'name' => 'Default Theme',
        'description' => 'Clean blue color scheme with modern styling',
        'preview' => '../../images/theme-previews/default.png',
        'colors' => ['#3498db', '#2c3e50', '#ecf0f1']
    ],
    'dark' => [
        'name' => 'Dark Theme', 
        'description' => 'Dark background with orange accents for night viewing',
        'preview' => '../../images/theme-previews/dark.png',
        'colors' => ['#2c3e50', '#f39c12', '#34495e']
    ],
    'light' => [
        'name' => 'Light Theme',
        'description' => 'Minimal light theme with subtle colors',
        'preview' => '../../images/theme-previews/light.png',
        'colors' => ['#ecf0f1', '#17a2b8', '#28a745']
    ]
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error_message = "Security token validation failed. Please try again.";
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'set_default_theme') {
            $new_theme = $_POST['theme'] ?? '';
            
            if (!array_key_exists($new_theme, $themes)) {
                $error_message = "Invalid theme selected.";
            } else {
                if (updateSiteSetting('default_theme', $new_theme, 'Default theme for new visitors')) {
                    $current_theme = $new_theme;
                    $success_message = "Default theme changed to {$themes[$new_theme]['name']}.";
                    
                    // Log admin activity
                    logAdminActivity('update_setting', 'site_settings', null, "Changed default theme to $new_theme");
                } else {
                    $error_message = "Failed to update default theme.";
                }
            }
        } elseif ($action === 'update_theme_settings') {
            $settings_updated = 0;
            
            // Site name
            $site_name = sanitizeInput($_POST['site_name'] ?? '');
            if (!empty($site_name) && updateSiteSetting('site_name', $site_name, 'Website name displayed in header')) {
                $settings_updated++;
            }
            
            // Site tagline
            $site_tagline = sanitizeInput($_POST['site_tagline'] ?? '');
            if (!empty($site_tagline) && updateSiteSetting('site_tagline', $site_tagline, 'Website tagline')) {
                $settings_updated++;
            }
            
            if ($settings_updated > 0) {
                $success_message = "Theme settings updated successfully.";
                logAdminActivity('update_settings', 'site_settings', null, "Updated $settings_updated theme settings");
            } else {
                $error_message = "No settings were updated.";
            }
        }
    }
}

// Get current site settings
$site_name = getSiteSetting('site_name', 'AutoDeals');
$site_tagline = getSiteSetting('site_tagline', 'Find Your Perfect Pre-Owned Vehicle');

// Generate CSRF token
$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Settings - AutoDeals Administration</title>
    <link rel="stylesheet" href="../../css/theme-<?php echo $current_theme; ?>.css" id="theme-link">
    <link rel="stylesheet" href="../admin-styles.css">
    <style>
        .theme-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        
        .theme-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .theme-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        .theme-card.active {
            border-color: #667eea;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        }
        
        .theme-preview {
            height: 200px;
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .theme-preview-content {
            text-align: center;
            z-index: 2;
        }
        
        .theme-preview-colors {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
        }
        
        .color-bar {
            flex: 1;
            opacity: 0.8;
        }
        
        .theme-info {
            padding: 1.5rem;
        }
        
        .theme-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }
        
        .theme-description {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .theme-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .theme-status {
            background: #28a745;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }
    </style>
</head>
<body class="admin-body">
    <div class="admin-layout">
        <!-- Admin Sidebar -->
        <nav class="admin-sidebar">
            <div class="admin-sidebar-header">
                <div class="admin-logo">
                    <span class="logo-icon">🚗</span>
                    <span class="logo-text"><?php echo sanitizeOutput($site_name); ?></span>
                </div>
                <div class="admin-subtitle">Administration</div>
            </div>
            
            <div class="admin-user-info">
                <div class="admin-avatar">👤</div>
                <div class="admin-details">
                    <div class="admin-name"><?php echo sanitizeOutput(getUserDisplayName()); ?></div>
                    <div class="admin-role">Administrator</div>
                </div>
            </div>
            
            <ul class="admin-nav">
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="cars.php">🚗 Manage Cars</a></li>
                <li><a href="users.php">👥 Manage Users</a></li>
                <li><a href="themes.php" class="active">🎨 Theme Settings</a></li>
                <li><a href="settings.php">⚙️ Site Settings</a></li>
                <li><a href="logs.php">📋 Activity Logs</a></li>
                <li class="nav-divider"></li>
                <li><a href="../index.html">🌐 View Site</a></li>
                <li><a href="../logout.php">🚪 Logout</a></li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>Theme Settings</h1>
                <div class="admin-header-actions">
                    <span class="admin-time">Current: <?php echo $themes[$current_theme]['name']; ?></span>
                </div>
            </div>
            
            <!-- Success/Error Messages -->
            <?php if ($success_message): ?>
                <div class="admin-alert admin-alert-success">
                    ✅ <?php echo sanitizeOutput($success_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="admin-alert admin-alert-danger">
                    ❌ <?php echo sanitizeOutput($error_message); ?>
                </div>
            <?php endif; ?>
            
            <!-- Current Theme Info -->
            <div class="admin-section">
                <h2>Current Default Theme</h2>
                <div class="admin-alert admin-alert-info">
                    <strong>🎨 <?php echo $themes[$current_theme]['name']; ?></strong> is currently set as the default theme.<br>
                    This theme will be applied to all new visitors and users who haven't manually selected a different theme.
                </div>
            </div>
            
            <!-- Theme Selection -->
            <div class="admin-section">
                <h2>Choose Default Theme</h2>
                <p>Select the theme that will be used by default for all visitors to your website.</p>
                
                <form method="POST" id="theme-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="set_default_theme">
                    <input type="hidden" name="theme" id="selected-theme" value="<?php echo $current_theme; ?>">
                    
                    <div class="theme-preview-grid">
                        <?php foreach ($themes as $theme_key => $theme): ?>
                        <div class="theme-card <?php echo $theme_key === $current_theme ? 'active' : ''; ?>" 
                             onclick="selectTheme('<?php echo $theme_key; ?>')">
                            <div class="theme-preview">
                                <div class="theme-preview-colors">
                                    <?php foreach ($theme['colors'] as $color): ?>
                                        <div class="color-bar" style="background-color: <?php echo $color; ?>"></div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="theme-preview-content">
                                    <div style="background: rgba(255,255,255,0.9); padding: 1rem; border-radius: 8px;">
                                        <div style="font-weight: bold; color: <?php echo $theme['colors'][1]; ?>"><?php echo $theme['name']; ?></div>
                                        <div style="font-size: 0.8rem; color: #666; margin-top: 0.5rem;">Sample Content</div>
                                    </div>
                                </div>
                            </div>
                            <div class="theme-info">
                                <div class="theme-name">
                                    <?php echo $theme['name']; ?>
                                    <?php if ($theme_key === $current_theme): ?>
                                        <span class="theme-status">Active</span>
                                    <?php endif; ?>
                                </div>
                                <div class="theme-description"><?php echo $theme['description']; ?></div>
                                <div class="theme-actions">
                                    <?php if ($theme_key !== $current_theme): ?>
                                        <button type="button" class="admin-btn admin-btn-sm admin-btn-primary" 
                                                onclick="selectTheme('<?php echo $theme_key; ?>')">
                                            Select Theme
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" class="admin-btn admin-btn-sm admin-btn-secondary" 
                                            onclick="previewTheme('<?php echo $theme_key; ?>')">
                                        Preview
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </form>
            </div>
            
            <!-- Theme-Related Settings -->
            <div class="admin-section">
                <h2>Theme-Related Settings</h2>
                <p>Customize the text and branding that appears with your selected theme.</p>
                
                <form method="POST" class="admin-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="update_theme_settings">
                    
                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="site_name">Site Name</label>
                            <input type="text" id="site_name" name="site_name" 
                                   value="<?php echo sanitizeOutput($site_name); ?>" 
                                   placeholder="AutoDeals">
                            <small style="color: #6c757d;">This appears in the header and navigation</small>
                        </div>
                        
                        <div class="admin-form-group">
                            <label for="site_tagline">Site Tagline</label>
                            <input type="text" id="site_tagline" name="site_tagline" 
                                   value="<?php echo sanitizeOutput($site_tagline); ?>" 
                                   placeholder="Find Your Perfect Pre-Owned Vehicle">
                            <small style="color: #6c757d;">This appears below the main heading</small>
                        </div>
                    </div>
                    
                    <button type="submit" class="admin-btn admin-btn-primary">
                        💾 Update Settings
                    </button>
                </form>
            </div>
            
            <!-- Theme Instructions -->
            <div class="admin-section">
                <h2>Theme Instructions</h2>
                <div class="admin-alert admin-alert-info">
                    <h4>How Themes Work:</h4>
                    <ul style="margin: 0.5rem 0 0 1rem;">
                        <li><strong>Default Theme:</strong> Applied to all new visitors and users who haven't made a selection</li>
                        <li><strong>User Preferences:</strong> Logged-in users can override the default with their personal choice</li>
                        <li><strong>Theme Switcher:</strong> Available on all pages for users to change themes instantly</li>
                        <li><strong>Persistence:</strong> User theme preferences are saved in their browser</li>
                    </ul>
                </div>
                
                <div style="margin-top: 1.5rem;">
                    <h4>Available Themes:</h4>
                    <ul style="margin: 0.5rem 0 0 1rem;">
                        <li><strong>Default Theme:</strong> Professional blue color scheme suitable for business use</li>
                        <li><strong>Dark Theme:</strong> Dark mode with orange accents, easier on the eyes in low light</li>
                        <li><strong>Light Theme:</strong> Clean, minimal design with subtle colors for a modern look</li>
                    </ul>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        function selectTheme(themeKey) {
            // Update form
            document.getElementById('selected-theme').value = themeKey;
            
            // Update UI
            document.querySelectorAll('.theme-card').forEach(card => {
                card.classList.remove('active');
            });
            event.currentTarget.classList.add('active');
            
            // Submit form
            if (confirm('Set this as the default theme for all new visitors?')) {
                document.getElementById('theme-form').submit();
            }
        }
        
        function previewTheme(themeKey) {
            // Change the current page theme for preview
            const themeLink = document.getElementById('theme-link');
            themeLink.href = `../../css/theme-${themeKey}.css`;
            
            // Show notification
            showNotification(`Previewing ${themeKey} theme. Refresh to return to default.`);
        }
        
        function showNotification(message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = 'admin-alert admin-alert-info';
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                max-width: 300px;
                animation: slideIn 0.3s ease-out;
            `;
            notification.innerHTML = message;
            
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // CSS for notification animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>