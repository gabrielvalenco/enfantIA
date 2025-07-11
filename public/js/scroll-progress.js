document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.getElementById('scrollProgressBar');
    
    if (progressBar) {
        // Update progress bar width based on scroll position
        function updateProgressBar() {
            // Calculate how far the user has scrolled
            const scrollTop = window.scrollY || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight;
            const clientHeight = document.documentElement.clientHeight;
            
            // Calculate the percentage scrolled
            const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
            
            // Update the width of the progress bar
            progressBar.style.width = scrollPercentage + '%';
        }
        
        // Add scroll event listener
        window.addEventListener('scroll', updateProgressBar);
        
        // Initialize progress bar on page load
        updateProgressBar();
    }
});
