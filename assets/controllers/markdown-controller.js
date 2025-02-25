import { Controller } from '@hotwired/stimulus';
import markdownit from 'markdown-it'
import { editor } from 'monaco-editor';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['input', 'preview', 'editorContainer', 'editor'];

    connect() {
        const model = editor.createModel(this.inputTarget.value, 'markdown');
        
        this.editorInstance = editor.create(this.editorTarget, {
            model,
            theme: 'vs-dark',
            automaticLayout: true,
            scrollBeyondLastLine: false,
            onDidContentSizeChange: () => {
                this.adaptLayout();
            },
        });

        
        model.onDidChangeContent(() => {
            const value = model.getValue();
            this.inputTarget.value = this.editorInstance.getValue();
            this.render();
        });

        this.editorInstance.onDidContentSizeChange(() => {
            this.adaptLayout();
        });

        this.adaptLayout();
    }

    adaptLayout() {
        const height = this.editorInstance.getContentHeight() < 200 ? 200 : this.editorInstance.getContentHeight();
        this.editorContainerTarget.style.height = `${height}px`;
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
