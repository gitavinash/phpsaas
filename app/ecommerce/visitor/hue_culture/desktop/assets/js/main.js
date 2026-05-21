/**
 * Hue Culture Global Script
 * Handles global interactions like "Coming Soon" notifications.
 */

document.addEventListener("DOMContentLoaded", () => {
    initComingSoonLinks();
});

/**
 * Initializes listeners for links that are currently under development.
 */
function initComingSoonLinks() {
    const selector = 'a[href*="training.html"], a[href*="community.html"]';
    const comingSoonLinks = document.querySelectorAll(selector);

    comingSoonLinks.forEach(link => {
        link.addEventListener("click", (e) => {
            e.preventDefault();
            showHueToast("Coming Soon: We're perfecting this experience for you.");
        });
    });
}

/**
 * Shows a premium toast notification.
 * @param {string} message 
 */
function showHueToast(message) {
    // Remove existing toast if present
    const existing = document.querySelector(".hue-toast");
    if (existing) existing.remove();

    // Create toast element
    const toast = document.createElement("div");
    toast.className = "hue-toast";
    toast.innerHTML = `
        <i class="fa-solid fa-circle-info"></i>
        <span>${message}</span>
    `;

    document.body.appendChild(toast);

    // Trigger animation
    setTimeout(() => {
        toast.classList.add("show");
    }, 10);

    // Auto-remove after 4 seconds
    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => {
            toast.remove();
        }, 600);
    }, 4000);
}
