import { Controller } from '@hotwired/stimulus';
import markdownit from 'markdown-it'

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['input', 'preview'];

    render() {
        const md = markdownit();
        const rendered = md.render(this.inputTarget.value);
        this.previewTarget.innerHTML = rendered;
    }
}
