import { Controller } from '@hotwired/stimulus';
import markdownit from 'markdown-it'
import loader from '@monaco-editor/loader';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['input', 'preview', 'editorContainer', 'editor'];

    connect() {
        loader.init().then((monaco) => {
            this.editorInstance = monaco.editor.create(this.editorTarget, {
                value: this.inputTarget.value,
                language: 'markdown',
                automaticLayout: true,
                scrollBeyondLastLine: false,
                minimap: {
                    enabled: false
                },
            });

            const model = this.editorInstance.getModel();

            model.onDidChangeContent(() => {
                this.inputTarget.value = this.editorInstance.getValue();
                this.render();
                this.adaptLayout();
            });
        })
        .then(() => {
            this.adaptLayout(10);
        });
    }

    adaptLayout(additionalHeight = 0) {
        const contentHeight = this.editorInstance.getContentHeight();
        const height = contentHeight < 200 ? 200 : contentHeight;
        this.editorContainerTarget.style.height = `${height + additionalHeight}px`;
    }
    
    render() {
        const md = markdownit();
        const rendered = md.render(this.inputTarget.value);
        this.previewTarget.innerHTML = rendered;
    }

    disconnect() {
        if (this.editorInstance) {
            this.editorInstance.dispose();
        }
    }
}
