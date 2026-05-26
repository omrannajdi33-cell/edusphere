import { moduleAutosave, splitReadingParagraphs } from './shared';

export function lectureModule(config) {
    const paragraphs = splitReadingParagraphs(config.readingText);
    const saved = config.saved ?? {};

    return {
        view: config.initialView ?? 'reading',
        zoom: saved.zoom ?? 100,
        lineMode: saved.lineMode ?? false,
        lineIndex: saved.lineIndex ?? 0,
        highlightMode: saved.highlightMode ?? false,
        highlights: saved.highlights ?? [],
        paragraphs,
        hasReading: config.hasReading,
        ...moduleAutosave({
            url: config.progressUrl,
            csrf: config.csrf,
            getPayload() {
                return {
                    zoom: this.zoom,
                    lineMode: this.lineMode,
                    lineIndex: this.lineIndex,
                    highlightMode: this.highlightMode,
                    highlights: this.highlights,
                };
            },
        }),

        init() {
            if (!this.hasReading) {
                this.view = 'exercise';
            }
        },

        openReading() {
            this.view = 'reading';
        },

        openExercise() {
            this.view = 'exercise';
        },

        zoomIn() {
            this.zoom = Math.min(this.zoom + 10, 200);
            this.scheduleSave();
        },

        zoomOut() {
            this.zoom = Math.max(this.zoom - 10, 80);
            this.scheduleSave();
        },

        toggleLineMode() {
            this.lineMode = !this.lineMode;
            this.lineIndex = 0;
            this.scheduleSave();
        },

        nextLine() {
            if (this.lineIndex < this.paragraphs.length - 1) {
                this.lineIndex += 1;
                this.scheduleSave();
            }
        },

        prevLine() {
            if (this.lineIndex > 0) {
                this.lineIndex -= 1;
                this.scheduleSave();
            }
        },

        toggleHighlightMode() {
            this.highlightMode = !this.highlightMode;
            this.scheduleSave();
        },

        highlightSelection() {
            const selection = window.getSelection();
            const text = selection?.toString().trim();

            if (!text) {
                return;
            }

            this.highlights.push(text);
            selection.removeAllRanges();
            this.scheduleSave();
        },

        removeHighlight(index) {
            this.highlights.splice(index, 1);
            this.scheduleSave();
        },
    };
}

export function lectureIslamiqueModule(config) {
    const base = lectureModule(config);

    return {
        ...base,
        audioPlaying: false,
        segmentIndex: config.saved?.segmentIndex ?? 0,
        segments: config.segments ?? [],

        playSegment(index) {
            this.segmentIndex = index;
            const audio = this.$refs.segmentAudio;

            if (!audio) {
                return;
            }

            audio.currentTime = this.segments[index]?.start ?? 0;
            audio.play();
            this.audioPlaying = true;
            this.scheduleSave();
        },

        toggleAudio() {
            const audio = this.$refs.segmentAudio;

            if (!audio) {
                return;
            }

            if (audio.paused) {
                audio.play();
                this.audioPlaying = true;
            } else {
                audio.pause();
                this.audioPlaying = false;
            }
        },
    };
}
