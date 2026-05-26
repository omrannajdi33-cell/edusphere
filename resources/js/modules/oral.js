import { moduleAutosave } from './shared';

export function oralRecorder(config) {
    const saved = config.saved ?? {};

    return {
        recordings: saved.recordings ?? [],
        recording: false,
        playingIndex: null,
        mediaRecorder: null,
        chunks: [],
        error: null,
        ...moduleAutosave({
            url: config.progressUrl,
            csrf: config.csrf,
            getPayload() {
                return { recordings: this.recordings };
            },
        }),

        async startRecording() {
            this.error = null;

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.chunks = [];
                this.mediaRecorder = new MediaRecorder(stream);
                this.mediaRecorder.ondataavailable = (event) => {
                    if (event.data.size > 0) {
                        this.chunks.push(event.data);
                    }
                };
                this.mediaRecorder.onstop = () => {
                    const blob = new Blob(this.chunks, { type: 'audio/webm' });
                    const reader = new FileReader();
                    reader.onloadend = () => {
                        this.recordings.push({
                            id: Date.now(),
                            data: reader.result,
                            createdAt: new Date().toISOString(),
                        });
                        stream.getTracks().forEach((track) => track.stop());
                        this.scheduleSave();
                    };
                    reader.readAsDataURL(blob);
                };
                this.mediaRecorder.start();
                this.recording = true;
            } catch {
                this.error = 'Microphone inaccessible. Vérifie les autorisations.';
            }
        },

        stopRecording() {
            if (this.mediaRecorder && this.recording) {
                this.mediaRecorder.stop();
                this.recording = false;
            }
        },

        play(index) {
            const audio = this.$refs[`audio-${index}`];
            if (!audio) {
                return;
            }

            audio.play();
            this.playingIndex = index;
        },

        remove(index) {
            this.recordings.splice(index, 1);
            this.scheduleSave();
        },
    };
}
