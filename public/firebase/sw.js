
self.addEventListener("push", (event) => {
    const notif = event.data.json().notification || {};

    event.waitUntil(
        self.registration.showNotification(notif.title || "New Notification", {
            body: notif.body || "You have a new message.",
            icon: notif.image || "default-icon.png",
            data: {
                url: notif.click_action || "/"
            }
        })
    );
});

self.addEventListener("notificationclick", (event) => {
    const targetUrl = event.notification.data?.url || "/";
    event.notification.close();
    event.waitUntil(clients.openWindow(targetUrl));
});
