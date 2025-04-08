import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  /**
   * Copie la chaîne de caractères fournie dans le presse-papiers.
   * @param {Event} event - L'événement déclenché, si pertinent.
   */
  copyToClipboard(event) {
    event.preventDefault();

    const button = event.target;
    const copyIcon = button.querySelector('#copyIcon');
    const successIcon = button.querySelector('#successIcon');

    const textToCopy = String(event?.target.dataset.clipboardText || this.textValue || "");

    const defaultColorClass = "btn-outline-secondary";
    const successColorClass = "btn-outline-success";
    const errorColorClass = "btn-outline-danger";
    const active = "opacity-100";
    const unactive = "opacity-0";

    button.classList.remove(successColorClass);
    button.classList.add(defaultColorClass);

    if (textToCopy) {
      navigator.clipboard.writeText(textToCopy)
        .then(() => {
          this.dispatch("success", { detail: { text: textToCopy } });

          setTimeout(() => {
            button.classList.remove(defaultColorClass);
            button.classList.add(successColorClass);
            copyIcon.classList.remove(active);
            copyIcon.classList.add(unactive);
            successIcon.classList.remove(unactive);
            successIcon.classList.add(active);
            
            setTimeout(() => {
              button.classList.remove(successColorClass);
              button.classList.add(defaultColorClass);
              copyIcon.classList.remove(unactive);
              copyIcon.classList.add(active);
              successIcon.classList.remove(active);
              successIcon.classList.add(unactive);
            }, 1000);
          }, 1000);
        })
        .catch(err => {
          console.error("Erreur lors de la copie dans le presse-papiers :", err);
          this.dispatch("error", { detail: { error: err } });

          setTimeout(() => {
            button.classList.remove(defaultColorClass);
            button.classList.add(errorColorClass);
            copyIcon.classList.remove(active);
            copyIcon.classList.add(unactive);
            successIcon.classList.remove(unactive);
            successIcon.classList.add(active);
            
            setTimeout(() => {
              button.classList.remove(errorColorClass);
              button.classList.add(defaultColorClass);
              copyIcon.classList.remove(unactive);
              copyIcon.classList.add(active);
              successIcon.classList.remove(active);
              successIcon.classList.add(unactive); 
            }, 1000);
          }, 1000);
        });
    } else {
      console.warn("Aucun texte à copier");
    }
  }
}
