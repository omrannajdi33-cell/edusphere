import { countWords, moduleAutosave } from './shared';

export function ecritureEditor(config) {
    const saved = config.saved ?? {};

    return {
        content: saved.content ?? config.initialContent ?? '',
        wordCount: saved.wordCount ?? 0,
        fontSize: saved.fontSize ?? 18,
        ...moduleAutosave({
            url: config.progressUrl,
            csrf: config.csrf,
            getPayload() {
                return {
                    content: this.content,
                    wordCount: this.wordCount,
                    fontSize: this.fontSize,
                };
            },
        }),

        init() {
            this.$nextTick(() => {
                if (this.$refs.editor && this.content) {
                    this.$refs.editor.innerHTML = this.content;
                }
                this.updateWordCount();
            });
        },

        exec(command, value = null) {
            this.$refs.editor?.focus();
            document.execCommand(command, false, value);
            this.syncContent();
        },

        setFontSize(size) {
            this.fontSize = size;
            this.exec('fontSize', '4');
            this.syncContent();
        },

        syncContent() {
            this.content = this.$refs.editor?.innerHTML ?? '';
            this.updateWordCount();
            this.scheduleSave();
        },

        updateWordCount() {
            this.wordCount = countWords(this.content);
        },

        undo() {
            document.execCommand('undo');
            this.syncContent();
        },

        redo() {
            document.execCommand('redo');
            this.syncContent();
        },
    };
}
