import { moduleAutosave } from './shared';

export function checklistProgress(config) {
    const saved = config.saved ?? {};

    return {
        items: saved.items ?? config.items ?? [],
        reflection: saved.reflection ?? '',
        ...moduleAutosave({
            url: config.progressUrl,
            csrf: config.csrf,
            getPayload() {
                return {
                    items: this.items,
                    reflection: this.reflection,
                };
            },
        }),

        get completedCount() {
            return this.items.filter((item) => item.done).length;
        },

        get progressPercent() {
            if (this.items.length === 0) {
                return 0;
            }

            return Math.round((this.completedCount / this.items.length) * 100);
        },

        toggle(id) {
            const item = this.items.find((entry) => entry.id === id);
            if (item) {
                item.done = !item.done;
                this.scheduleSave();
            }
        },

        saveReflection() {
            this.scheduleSave();
        },
    };
}

export function calculModule(config) {
    const saved = config.saved ?? {};

    return {
        scratch: saved.scratch ?? '',
        showScratch: false,
        ...moduleAutosave({
            url: config.progressUrl,
            csrf: config.csrf,
            getPayload() {
                return { scratch: this.scratch };
            },
        }),

        appendDigit(digit) {
            this.scratch += digit;
            this.scheduleSave();
        },

        clearScratch() {
            this.scratch = '';
            this.scheduleSave();
        },
    };
}
