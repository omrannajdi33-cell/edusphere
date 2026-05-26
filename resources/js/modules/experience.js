import { moduleAutosave } from './shared';

export function experienceLab(config) {
    const saved = config.saved ?? {};

    return {
        steps: saved.steps ?? config.defaultSteps ?? [
            { id: 1, title: 'Hypothèse', done: false },
            { id: 2, title: 'Matériel', done: false },
            { id: 3, title: 'Protocole', done: false },
            { id: 4, title: 'Observations', done: false },
            { id: 5, title: 'Conclusion', done: false },
        ],
        rows: saved.rows ?? [
            { time: '', observation: '', measure: '' },
            { time: '', observation: '', measure: '' },
            { time: '', observation: '', measure: '' },
        ],
        conclusion: saved.conclusion ?? '',
        ...moduleAutosave({
            url: config.progressUrl,
            csrf: config.csrf,
            getPayload() {
                return {
                    steps: this.steps,
                    rows: this.rows,
                    conclusion: this.conclusion,
                };
            },
        }),

        toggleStep(id) {
            const step = this.steps.find((item) => item.id === id);
            if (step) {
                step.done = !step.done;
                this.scheduleSave();
            }
        },

        addRow() {
            this.rows.push({ time: '', observation: '', measure: '' });
            this.scheduleSave();
        },

        saveTable() {
            this.scheduleSave();
        },

        saveConclusion() {
            this.scheduleSave();
        },
    };
}
