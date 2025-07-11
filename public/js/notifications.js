document.addEventListener('DOMContentLoaded', function() {
    // Get the notification icon element
    const notificationIcon = document.getElementById('notificationIcon');
    
    if (notificationIcon) {
        // Get data from server-side session (passed via data attributes)
        const currentNotificationCount = parseInt(notificationIcon.getAttribute('data-count') || '0');
        const lastNotificationCount = parseInt(notificationIcon.getAttribute('data-last-count') || '0');
        const notificationsSeen = notificationIcon.getAttribute('data-seen') === 'true';
        
        // Check if we have pending invitations
        const hasPendingInvitations = currentNotificationCount > 0;
        
        // Check if we have new notifications (more than before)
        const hasNewNotifications = currentNotificationCount > lastNotificationCount;
        
        // Get the current page URL to check if we're on the notifications page
        const isNotificationsPage = window.location.href.includes('/notifications');
        
        // Handle click event to stop pulsing
        notificationIcon.addEventListener('click', function() {
            // Remove the pulse animation immediately when clicked
            this.classList.remove('pulse-animation');
            
            // We don't need to update session here as the controller will do that
            // when the notifications page loads
        });
        
        // If we're on the notifications page, no need to pulse
        if (isNotificationsPage) {
            notificationIcon.classList.remove('pulse-animation');
        }
        // If we have new notifications or unseen notifications, make it pulse
        else if (hasPendingInvitations && (hasNewNotifications || !notificationsSeen)) {
            notificationIcon.classList.add('pulse-animation');
        }
        // Otherwise, don't pulse
        else {
            notificationIcon.classList.remove('pulse-animation');
        }
    }
});
