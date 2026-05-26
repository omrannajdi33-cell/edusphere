import { moduleAutosave } from './shared';

export function geometrieBoard(config) {
    const saved = config.saved ?? {};

    return {
        shapes: saved.shapes ?? [
            { id: 1, type: 'triangle', x: 80, y: 80, label: 'A' },
            { id: 2, type: 'circle', x: 220, y: 120, label: 'B' },
            { id: 3, type: 'rectangle', x: 360, y: 90, label: 'C' },
        ],
        lines: saved.lines ?? [],
        activeTool: 'select',
        drawingLine: false,
        lineStart: null,
        dragShape: null,
        nextId: saved.nextId ?? 4,
        ...moduleAutosave({
            url: config.progressUrl,
            csrf: config.csrf,
            getPayload() {
                return {
                    shapes: this.shapes,
                    lines: this.lines,
                    nextId: this.nextId,
                };
            },
        }),

        addShape(type) {
            this.shapes.push({
                id: this.nextId++,
                type,
                x: 120 + this.shapes.length * 20,
                y: 120,
                label: String.fromCharCode(65 + this.shapes.length),
            });
            this.scheduleSave();
        },

        startDrag(event, shape) {
            if (this.activeTool !== 'select') {
                return;
            }

            this.dragShape = shape;
            this.dragOffset = { x: event.offsetX - shape.x, y: event.offsetY - shape.y };
        },

        drag(event) {
            if (!this.dragShape) {
                return;
            }

            this.dragShape.x = event.offsetX - this.dragOffset.x;
            this.dragShape.y = event.offsetY - this.dragOffset.y;
        },

        endDrag() {
            if (this.dragShape) {
                this.dragShape = null;
                this.scheduleSave();
            }
        },

        canvasClick(event) {
            if (this.activeTool !== 'line') {
                return;
            }

            const point = { x: event.offsetX, y: event.offsetY };

            if (!this.drawingLine) {
                this.lineStart = point;
                this.drawingLine = true;
            } else {
                this.lines.push({ from: this.lineStart, to: point });
                this.lineStart = null;
                this.drawingLine = false;
                this.scheduleSave();
            }
        },

        removeShape(id) {
            this.shapes = this.shapes.filter((shape) => shape.id !== id);
            this.scheduleSave();
        },
    };
}
