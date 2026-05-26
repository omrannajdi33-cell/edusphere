import { moduleAutosave } from './shared';

export function timelineSorter(config) {
    const saved = config.saved ?? {};
    const events = saved.events ?? config.events ?? [];

    return {
        events,
        dragging: null,
        ...moduleAutosave({
            url: config.progressUrl,
            csrf: config.csrf,
            getPayload() {
                return { events: this.events };
            },
        }),

        dragStart(index) {
            this.dragging = index;
        },

        drop(index) {
            if (this.dragging === null || this.dragging === index) {
                return;
            }

            const moved = this.events.splice(this.dragging, 1)[0];
            this.events.splice(index, 0, moved);
            this.dragging = null;
            this.scheduleSave();
        },

        shuffle() {
            for (let i = this.events.length - 1; i > 0; i -= 1) {
                const j = Math.floor(Math.random() * (i + 1));
                [this.events[i], this.events[j]] = [this.events[j], this.events[i]];
            }
            this.scheduleSave();
        },
    };
}
