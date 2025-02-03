import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ["notifications", "notificationList", "notification"];

  connect() {
    this.showNotification();
  }

  showNotification() {
    if (this.notificationsTarget.checked) {
      this.notificationListTarget.style.display = 'block';
    } else {
      this.notificationListTarget.style.display = 'none';
    }
  }

  syncNotification() {
    if (this.hasNotificationTarget) {
      this.notificationTarget.checked = this.notificationsTarget.checked;
    }
  }

  toggle() {
    this.showNotification();
    this.syncNotification();
  }
}
