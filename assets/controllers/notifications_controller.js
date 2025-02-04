import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ["notifications", "notificationList", "notification"];

  connect() {
    this.showNotification();
  }

  showNotification() {
    this.notificationListTarget.style.display = this.notificationsTarget.checked ? 'block' : 'none';
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
