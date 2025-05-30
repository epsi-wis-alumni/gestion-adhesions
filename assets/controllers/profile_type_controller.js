import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["studentCard", "degree", "typeInput"];

  connect() {
    this.toggleFields();
  }

  toggleFields() {
    const selected = this.typeInputTargets.find(input => input.checked)?.value;

    const studentInput = this.studentCardTarget.querySelector('input[type="file"]');
    const degreeInput = this.degreeTarget.querySelector('input[type="file"]');

    this.studentCardTarget.classList.add("d-none");
    this.degreeTarget.classList.add("d-none");

    if (studentInput) studentInput.required = false;
    if (degreeInput) degreeInput.required = false;

    if (selected === "1") { // Étudiant
      this.studentCardTarget.classList.remove("d-none");
      if (studentInput) studentInput.required = true;
    } else if (selected === "2") { // Alumni
      this.degreeTarget.classList.remove("d-none");
      if (degreeInput) degreeInput.required = true;
    }
  }
}
