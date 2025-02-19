import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["collectionContainer"]

    static values = {
        index    : Number,
        prototype: String,
    }

    connect() {
        this.collectionContainerTarget.querySelectorAll('li').forEach(item => {
            if (!item.parentElement.classList.contains('form-group')) {
                const wrapper = this.wrapInDiv(item);
                this.collectionContainerTarget.appendChild(wrapper);
                this.addDeleteButton(wrapper);
            }
        });
    }

    addCollectionElement(event) {
        const item = document.createElement('li');
        item.className = "col-11";
        item.innerHTML = this.prototypeValue.replace(/__name__/g, this.indexValue);

        const wrapper = this.wrapInDiv(item);
        this.collectionContainerTarget.appendChild(wrapper);

        this.indexValue++;
        this.addDeleteButton(wrapper);
    }

    wrapInDiv(item) {
        const wrapper = document.createElement('div');
        wrapper.className = "form-group mb-3 d-flex align-items-center";
        wrapper.appendChild(item);
        return wrapper;
    }

    addDeleteButton(wrapper) {
        const removeFormButton = document.createElement('a');
        removeFormButton.type = 'button';
        removeFormButton.className = 'bg-transparent border-0 link-danger p-2';

        const icon = document.createElement('i');
        icon.className = "fa-duotone fa-regular fa-trash-can fa-swap-opacity";
        removeFormButton.appendChild(icon);

        wrapper.appendChild(removeFormButton);

        removeFormButton.addEventListener('click', (e) => {
            e.preventDefault();
            wrapper.remove();
        });
    }
}
