import { Controller } from '@hotwired/stimulus';
import { Collapse } from 'bootstrap';

export default class extends Controller {
  static targets = ["notifications", 'notificationDetails'];
  
  connect() {
    this.notificationCollapse = new Collapse(
      this.notificationDetailsTarget,
      { toggle: this.notificationsTarget.checked}
    );
    this.showNotification();
  }

  showNotification() {
    this.notificationsTarget.checked ? this.notificationCollapse.show() : this.notificationCollapse.hide();
  }  

  syncNotification() {
    if (!this.notificationsTarget.checked) {
      const checkboxes = this.notificationDetailsTarget.querySelectorAll('input[type="checkbox"]');
      checkboxes.forEach(checkbox => checkbox.checked = this.notificationsTarget.checked);
    }
  }

  toggle() {
    this.showNotification();
    this.syncNotification();
  }
}
