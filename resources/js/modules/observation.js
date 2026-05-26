import { moduleAutosave } from './shared';

export function observationAnnotator(config) {
    const saved = config.saved ?? {};

    return {
        pins: saved.pins ?? [],
        notes: saved.notes ?? '',
        placing: false,
        label: '',
        ...moduleAutosave({
            url: config.progressUrl,
            csrf: config.csrf,
            getPayload() {
                return { pins: this.pins, notes: this.notes };
            },
        }),

        togglePlacing() {
            this.placing = !this.placing;
        },

        placePin(event) {
            if (!this.placing) {
                return;
            }

            const rect = event.currentTarget.getBoundingClientRect();
            this.pins.push({
                id: Date.now(),
                x: ((event.clientX - rect.left) / rect.width) * 100,
                y: ((event.clientY - rect.top) / rect.height) * 100,
                label: this.label || `Élément ${this.pins.length + 1}`,
            });
            this.label = '';
            this.placing = false;
            this.scheduleSave();
        },

        removePin(id) {
            this.pins = this.pins.filter((pin) => pin.id !== id);
            this.scheduleSave();
        },

        saveNotes() {
            this.scheduleSave();
        },
    };
}
