import { moduleAutosave } from './shared';

export function problemesWorkspace(config) {
    const saved = config.saved ?? {};

    return {
        tool: 'pen',
        showEnonce: false,
        showAnswer: false,
        drawing: false,
        ...moduleAutosave({
            url: config.progressUrl,
            csrf: config.csrf,
            getPayload() {
                return {
                    canvas: this.exportCanvas(),
                };
            },
        }),

        init() {
            this.$nextTick(() => {
                this.resizeCanvas();
                if (saved.canvas) {
                    this.loadCanvas(saved.canvas);
                }
            });
            window.addEventListener('resize', () => this.resizeCanvas());
        },

        resizeCanvas() {
            const canvas = this.$refs.canvas;
            if (!canvas) {
                return;
            }

            const rect = canvas.parentElement.getBoundingClientRect();
            const image = canvas.toDataURL();
            canvas.width = Math.max(1, Math.floor(rect.width));
            canvas.height = Math.max(1, Math.floor(rect.height));

            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            if (image !== 'data:,') {
                this.loadCanvas(image);
            }
        },

        pointerPos(event) {
            const canvas = this.$refs.canvas;
            const rect = canvas.getBoundingClientRect();

            return {
                x: (event.clientX - rect.left) * (canvas.width / rect.width),
                y: (event.clientY - rect.top) * (canvas.height / rect.height),
            };
        },

        applyToolStyle(ctx) {
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';

            if (this.tool === 'eraser') {
                ctx.globalCompositeOperation = 'destination-out';
                ctx.strokeStyle = 'rgba(0,0,0,1)';
                ctx.lineWidth = 28;
                return;
            }

            ctx.globalCompositeOperation = 'source-over';

            if (this.tool === 'highlighter') {
                ctx.strokeStyle = 'rgba(250, 204, 21, 0.55)';
                ctx.lineWidth = 26;
                return;
            }

            ctx.strokeStyle = '#2563eb';
            ctx.lineWidth = 3;
        },

        startDraw(event) {
            if (this.showAnswer) {
                return;
            }

            event.preventDefault();
            this.drawing = true;
            const ctx = this.$refs.canvas.getContext('2d');
            const { x, y } = this.pointerPos(event);
            this.applyToolStyle(ctx);
            ctx.beginPath();
            ctx.moveTo(x, y);
        },

        draw(event) {
            if (!this.drawing) {
                return;
            }

            event.preventDefault();
            const ctx = this.$refs.canvas.getContext('2d');
            const { x, y } = this.pointerPos(event);
            ctx.lineTo(x, y);
            ctx.stroke();
        },

        endDraw() {
            if (this.drawing) {
                this.drawing = false;
                this.scheduleSave();
            }
        },

        clearCanvas() {
            const canvas = this.$refs.canvas;
            const ctx = canvas.getContext('2d');
            ctx.globalCompositeOperation = 'source-over';
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            this.scheduleSave();
        },

        exportCanvas() {
            return this.$refs.canvas?.toDataURL() ?? null;
        },

        loadCanvas(dataUrl) {
            const canvas = this.$refs.canvas;
            const ctx = canvas.getContext('2d');
            const img = new Image();
            img.onload = () => {
                ctx.globalCompositeOperation = 'source-over';
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            };
            img.src = dataUrl;
        },
    };
}
