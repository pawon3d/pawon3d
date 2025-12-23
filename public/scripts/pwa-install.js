// PWA Install Handler
let deferredPrompt;
let installButton;

// Check if current page is a landing page
function isLandingPage() {
    const path = window.location.pathname;
    return path === '/' || path.startsWith('/landing-');
}

// Initialize PWA install functionality
document.addEventListener('DOMContentLoaded', function() {
    installButton = document.getElementById('pwa-install-btn');
    
    if (!installButton) {
        return; // No install button on this page
    }

    // Initially hide the button
    installButton.style.display = 'none';
    
    // If on landing page, keep button hidden (but PWA still installable via browser)
    if (isLandingPage()) {
        installButton.style.display = 'none';
    }
});

// Listen for beforeinstallprompt event
window.addEventListener('beforeinstallprompt', (e) => {
    // On landing pages, allow browser to show its own prompt
    // On other pages, prevent browser prompt and show custom button
    if (!isLandingPage()) {
        e.preventDefault();
    }
    
    // Stash the event so it can be triggered later
    deferredPrompt = e;
    
    // Show the install button only if NOT on landing page
    if (installButton && !isLandingPage()) {
        installButton.style.display = 'flex';
    }
});

// Handle install button click
function installPWA() {
    if (!deferredPrompt) {
        console.log('PWA already installed or prompt not available');
        return;
    }
    
    // Show the install prompt
    deferredPrompt.prompt();
    
    // Wait for the user to respond to the prompt
    deferredPrompt.userChoice.then((choiceResult) => {
        if (choiceResult.outcome === 'accepted') {
            console.log('User accepted the PWA install');
        } else {
            console.log('User dismissed the PWA install');
        }
        
        // Clear the deferredPrompt
        deferredPrompt = null;
        
        // Hide the install button
        if (installButton) {
            installButton.style.display = 'none';
        }
    });
}

// Listen for app installed event
window.addEventListener('appinstalled', () => {
    console.log('PWA was installed');
    
    // Hide the install button
    if (installButton) {
        installButton.style.display = 'none';
    }
    
    // Clear the deferredPrompt
    deferredPrompt = null;
});

