export function moduleAutosave(config) {
    let timer = null;
    let saving = false;

    return {
        saveStatus: 'idle',
        savedAt: config.savedAt ?? null,

        scheduleSave() {
            clearTimeout(timer);
            timer = setTimeout(() => this.saveNow(), config.debounceMs ?? 4000);
        },

        async saveNow() {
            if (saving || !config.url) {
                return;
            }

            saving = true;
            this.saveStatus = 'saving';

            try {
                const moduleData = config.getPayload
                    ? config.getPayload.call(this)
                    : (typeof config.payload === 'function' ? config.payload.call(this) : config.payload);

                const response = await fetch(config.url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': config.csrf,
                    },
                    body: JSON.stringify({ module_data: moduleData }),
                });

                if (!response.ok) {
                    throw new Error('save failed');
                }

                const data = await response.json();
                this.saveStatus = 'saved';
                this.savedAt = data.saved_at ?? new Date().toISOString();
            } catch {
                this.saveStatus = 'error';
            } finally {
                saving = false;
            }
        },
    };
}

export function countWords(html) {
    const text = html
        .replace(/<[^>]*>/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();

    if (!text) {
        return 0;
    }

    return text.split(' ').length;
}

export function splitReadingParagraphs(text) {
    return String(text || '')
        .split(/\n\s*\n/)
        .map((part) => part.trim())
        .filter(Boolean);
}
