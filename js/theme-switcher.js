// Theme Switcher JavaScript

// Theme configuration
const themes = {
    'default': '../css/theme-default.css',
    'dark': '../css/theme-dark.css',
    'light': '../css/theme-light.css'
};

// Initialize theme system when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeThemeSwitcher();
    loadSavedTheme();
});

// Initialize the theme switcher functionality
function initializeThemeSwitcher() {
    const themeSelect = document.getElementById('theme-select');
    
    if (themeSelect) {
        // Add event listener for theme changes
        themeSelect.addEventListener('change', function() {
            const selectedTheme = this.value;
            switchTheme(selectedTheme);
            saveTheme(selectedTheme);
        });
    }
}

// Switch to a specific theme
function switchTheme(themeName) {
    const themeLink = document.getElementById('theme-link');
    
    if (themeLink && themes[themeName]) {
        // Add smooth transition effect
        document.body.style.transition = 'all 0.3s ease';
        
        // Change the theme CSS file
        themeLink.href = themes[themeName];
        
        // Update the select dropdown
        const themeSelect = document.getElementById('theme-select');
        if (themeSelect) {
            themeSelect.value = themeName;
        }
        
        // Add theme class to body for additional styling if needed
        document.body.className = document.body.className.replace(/theme-\w+/g, '');
        document.body.classList.add(`theme-${themeName}`);
        
        console.log(`Theme switched to: ${themeName}`);
    }
}

// Save theme preference to localStorage
function saveTheme(themeName) {
    try {
        localStorage.setItem('selectedTheme', themeName);
        console.log(`Theme preference saved: ${themeName}`);
    } catch (error) {
        console.warn('Could not save theme preference:', error);
    }
}

// Load saved theme from localStorage
function loadSavedTheme() {
    try {
        const savedTheme = localStorage.getItem('selectedTheme');
        
        if (savedTheme && themes[savedTheme]) {
            switchTheme(savedTheme);
        } else {
            // Default to 'default' theme if no saved preference
            switchTheme('default');
        }
    } catch (error) {
        console.warn('Could not load saved theme preference:', error);
        // Fallback to default theme
        switchTheme('default');
    }
}

// Utility function to get current theme
function getCurrentTheme() {
    try {
        return localStorage.getItem('selectedTheme') || 'default';
    } catch (error) {
        return 'default';
    }
}

// Utility function to cycle through themes (for keyboard shortcuts)
function cycleTheme() {
    const currentTheme = getCurrentTheme();
    const themeKeys = Object.keys(themes);
    const currentIndex = themeKeys.indexOf(currentTheme);
    const nextIndex = (currentIndex + 1) % themeKeys.length;
    const nextTheme = themeKeys[nextIndex];
    
    switchTheme(nextTheme);
    saveTheme(nextTheme);
}

// Add keyboard shortcut for theme cycling (Ctrl+Shift+T)
document.addEventListener('keydown', function(event) {
    if (event.ctrlKey && event.shiftKey && event.key === 'T') {
        event.preventDefault();
        cycleTheme();
    }
});

// Export functions for external use if needed
window.ThemeSwitcher = {
    switchTheme: switchTheme,
    saveTheme: saveTheme,
    loadSavedTheme: loadSavedTheme,
    getCurrentTheme: getCurrentTheme,
    cycleTheme: cycleTheme,
    themes: themes
};