document.addEventListener('DOMContentLoaded', function() {
    // Theme toggle functionality
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        const themeIcon = themeToggle.querySelector('i');
        
        // Check if user previously set a theme preference
        const currentTheme = localStorage.getItem('theme') || 'dark';
        
        // Apply the saved theme or default to dark
        document.documentElement.setAttribute('data-theme', currentTheme);
        updateThemeIcon(currentTheme);
        
        // Toggle theme when button is clicked
        themeToggle.addEventListener('click', function() {
            // Get current theme
            const currentTheme = document.documentElement.getAttribute('data-theme');
            
            // Switch to the opposite theme
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            // Update the theme
            document.documentElement.setAttribute('data-theme', newTheme);
            
            // Save the theme preference
            localStorage.setItem('theme', newTheme);
            
            // Update the icon
            updateThemeIcon(newTheme);
        });
    }
});

// Function to update the theme icon
function updateThemeIcon(theme) {
    const themeIcon = document.querySelector('#theme-toggle i');
    if (!themeIcon) return;
    
    if (theme === 'dark') {
        themeIcon.className = 'fas fa-sun';
    } else {
        themeIcon.className = 'fas fa-moon';
    }
}