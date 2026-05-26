import * as pdfjsLib from 'pdfjs-dist';

pdfjsLib.GlobalWorkerOptions.workerSrc = new URL(
    'pdfjs-dist/build/pdf.worker.min.mjs',
    import.meta.url,
).toString();

const COLORS = {
    pen: '#1e40af',
    highlighter: '#facc15',
    teacher: '#dc2626',
};

function drawStroke(ctx, stroke) {
    const points = stroke.points ?? [];
    if (points.length < 2) {
        return;
    }

    ctx.save();
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = stroke.color ?? COLORS.pen;
    ctx.lineWidth = stroke.width ?? 3;
    ctx.globalAlpha = stroke.opacity ?? (stroke.tool === 'highlighter' ? 0.35 : 1);
    ctx.globalCompositeOperation = stroke.tool === 'eraser' ? 'destination-out' : 'source-over';

    ctx.beginPath();
    ctx.moveTo(points[0].x, points[0].y);
    for (let i = 1; i < points.length; i += 1) {
        ctx.lineTo(points[i].x, points[i].y);
    }
    ctx.stroke();
    ctx.restore();
}

function cloneStrokes(strokes) {
    return JSON.parse(JSON.stringify(strokes ?? []));
}

export function initWorksheetViewer(root, config) {
    const state = {
        pdf: null,
        pageCount: 0,
        currentPage: 0,
        scale: 1,
        tool: 'pen',
        readOnly: !!config.readOnly,
        mode: config.mode ?? 'student',
        layer: config.mode === 'teacher' ? 'teacher' : 'student',
        document: normalizeDocument(config.initialData),
        drawing: false,
        currentStroke: null,
        saveTimer: null,
        dirty: false,
    };

    const els = {
        pdfCanvas: root.querySelector('[data-ws-pdf]'),
        drawCanvas: root.querySelector('[data-ws-draw]'),
        teacherCanvas: root.querySelector('[data-ws-teacher]'),
        stage: root.querySelector('[data-ws-stage]'),
        status: root.querySelector('[data-ws-status]'),
        pageLabel: root.querySelector('[data-ws-page-label]'),
        prevBtn: root.querySelector('[data-ws-prev]'),
        nextBtn: root.querySelector('[data-ws-next]'),
        penBtn: root.querySelector('[data-ws-pen]'),
        highlighterBtn: root.querySelector('[data-ws-highlighter]'),
        eraserBtn: root.querySelector('[data-ws-eraser]'),
        undoBtn: root.querySelector('[data-ws-undo]'),
        submitBtn: root.querySelector('[data-ws-submit]'),
        alert: root.querySelector('[data-ws-alert]'),
    };

    const pdfCtx = els.pdfCanvas.getContext('2d');
    const drawCtx = els.drawCanvas.getContext('2d');
    const teacherCtx = els.teacherCanvas.getContext('2d');

    function normalizeDocument(data) {
        if (!data || typeof data !== 'object') {
            return { version: 1, pageCount: 0, pages: {} };
        }
        return {
            version: 1,
            pageCount: data.pageCount ?? 0,
            pages: data.pages ?? {},
        };
    }

    function pageKey(index) {
        return String(index);
    }

    function pageData(index) {
        const key = pageKey(index);
        if (!state.document.pages[key]) {
            state.document.pages[key] = { student: [], teacher: [] };
        }
        return state.document.pages[key];
    }

    function activeStrokes() {
        return pageData(state.currentPage)[state.layer] ?? [];
    }

    function setStatus(text, tone = 'muted') {
        if (!els.status) {
            return;
        }
        els.status.textContent = text;
        els.status.dataset.tone = tone;
    }

    function setAlert(text, tone = 'info') {
        if (!els.alert) {
            return;
        }
        if (!text) {
            els.alert.hidden = true;
            els.alert.textContent = '';
            return;
        }
        els.alert.hidden = false;
        els.alert.textContent = text;
        els.alert.dataset.tone = tone;
    }

    function resizeCanvases(viewport) {
        [els.pdfCanvas, els.drawCanvas, els.teacherCanvas].forEach((canvas) => {
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            canvas.style.width = `${viewport.width}px`;
            canvas.style.height = `${viewport.height}px`;
        });
        if (els.stage) {
            els.stage.style.width = `${viewport.width}px`;
            els.stage.style.height = `${viewport.height}px`;
        }
    }

    async function renderPdfPage(index) {
        const page = await state.pdf.getPage(index + 1);
        const viewport = page.getViewport({ scale: state.scale });
        resizeCanvases(viewport);
        await page.render({ canvasContext: pdfCtx, viewport }).promise;
        redrawOverlay();
    }

    function redrawOverlay() {
        drawCtx.clearRect(0, 0, els.drawCanvas.width, els.drawCanvas.height);
        teacherCtx.clearRect(0, 0, els.teacherCanvas.width, els.teacherCanvas.height);

        const data = pageData(state.currentPage);
        (data.student ?? []).forEach((stroke) => drawStroke(drawCtx, stroke));
        (data.teacher ?? []).forEach((stroke) => drawStroke(teacherCtx, stroke));

        if (state.currentStroke) {
            const ctx = state.layer === 'teacher' ? teacherCtx : drawCtx;
            drawStroke(ctx, state.currentStroke);
        }
    }

    function pointerPos(event) {
        const rect = els.drawCanvas.getBoundingClientRect();
        const scaleX = els.drawCanvas.width / rect.width;
        const scaleY = els.drawCanvas.height / rect.height;
        return {
            x: (event.clientX - rect.left) * scaleX,
            y: (event.clientY - rect.top) * scaleY,
        };
    }

    function startStroke(event) {
        if (state.readOnly || event.pointerType === 'mouse' && event.button !== 0) {
            return;
        }
        event.preventDefault();
        els.drawCanvas.setPointerCapture(event.pointerId);
        state.drawing = true;
        const point = pointerPos(event);
        state.currentStroke = {
            tool: state.tool,
            color: state.layer === 'teacher' ? COLORS.teacher : (state.tool === 'highlighter' ? COLORS.highlighter : COLORS.pen),
            width: state.tool === 'highlighter' ? 22 : state.tool === 'eraser' ? 28 : 3,
            opacity: state.tool === 'highlighter' ? 0.35 : 1,
            points: [point],
        };
        redrawOverlay();
    }

    function moveStroke(event) {
        if (!state.drawing || !state.currentStroke) {
            return;
        }
        event.preventDefault();
        state.currentStroke.points.push(pointerPos(event));
        redrawOverlay();
    }

    function endStroke(event) {
        if (!state.drawing || !state.currentStroke) {
            return;
        }
        event.preventDefault();
        state.drawing = false;
        const strokes = activeStrokes();
        strokes.push(state.currentStroke);
        pageData(state.currentPage)[state.layer] = strokes;
        state.currentStroke = null;
        state.dirty = true;
        redrawOverlay();
        scheduleSave();
    }

    function scheduleSave() {
        if (!config.saveUrl || state.readOnly) {
            return;
        }
        clearTimeout(state.saveTimer);
        state.saveTimer = setTimeout(saveAnnotations, 700);
        setStatus('Enregistrement…', 'pending');
    }

    async function saveAnnotations() {
        if (!config.saveUrl) {
            return;
        }
        try {
            const response = await fetch(config.saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrf,
                    Accept: 'application/json',
                },
                body: JSON.stringify({ annotations: state.document }),
            });
            if (!response.ok) {
                throw new Error('save_failed');
            }
            state.dirty = false;
            setStatus('Sauvegardé', 'ok');
        } catch {
            setStatus('Erreur de sauvegarde', 'error');
        }
    }

    async function submitWorksheet() {
        if (!config.submitUrl || state.readOnly) {
            return;
        }
        if (state.dirty) {
            await saveAnnotations();
        }
        if (!confirm('Envoyer ta feuille au professeur ?')) {
            return;
        }
        const response = await fetch(config.submitUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': config.csrf,
                Accept: 'application/json',
            },
            body: JSON.stringify({ annotations: state.document }),
        });
        if (response.ok) {
            window.location.href = config.resultUrl;
        }
    }

    function undoStroke() {
        if (state.readOnly) {
            return;
        }
        const strokes = activeStrokes();
        strokes.pop();
        pageData(state.currentPage)[state.layer] = strokes;
        state.dirty = true;
        redrawOverlay();
        scheduleSave();
    }

    function setTool(tool) {
        state.tool = tool;
        [els.penBtn, els.highlighterBtn, els.eraserBtn].forEach((btn) => {
            if (!btn) return;
            btn.dataset.active = btn.dataset.tool === tool ? 'true' : 'false';
        });
    }

    function updatePageLabel() {
        if (els.pageLabel) {
            els.pageLabel.textContent = `Page ${state.currentPage + 1} / ${state.pageCount}`;
        }
        if (els.prevBtn) {
            els.prevBtn.disabled = state.currentPage <= 0;
        }
        if (els.nextBtn) {
            els.nextBtn.disabled = state.currentPage >= state.pageCount - 1;
        }
    }

    async function goToPage(index) {
        if (index < 0 || index >= state.pageCount) {
            return;
        }
        state.currentPage = index;
        updatePageLabel();
        await renderPdfPage(index);
    }

    function bindEvents() {
        els.drawCanvas.addEventListener('pointerdown', startStroke);
        els.drawCanvas.addEventListener('pointermove', moveStroke);
        els.drawCanvas.addEventListener('pointerup', endStroke);
        els.drawCanvas.addEventListener('pointercancel', endStroke);

        els.prevBtn?.addEventListener('click', () => goToPage(state.currentPage - 1));
        els.nextBtn?.addEventListener('click', () => goToPage(state.currentPage + 1));
        els.penBtn?.addEventListener('click', () => setTool('pen'));
        els.highlighterBtn?.addEventListener('click', () => setTool('highlighter'));
        els.eraserBtn?.addEventListener('click', () => setTool('eraser'));
        els.undoBtn?.addEventListener('click', undoStroke);
        els.submitBtn?.addEventListener('click', submitWorksheet);

        window.addEventListener('resize', () => {
            clearTimeout(state.resizeTimer);
            state.resizeTimer = setTimeout(() => renderPdfPage(state.currentPage), 200);
        });
    }

    async function boot() {
        setTool('pen');
        setStatus('Chargement du document…');
        const loadingTask = pdfjsLib.getDocument(config.pdfUrl);
        state.pdf = await loadingTask.promise;
        state.pageCount = state.pdf.numPages;
        state.document.pageCount = state.pageCount;
        updatePageLabel();

        if (config.returned) {
            setAlert('Le professeur t\'a renvoyé cette feuille. Corrige tes erreurs en rouge puis renvoie.', 'warning');
        } else if (config.mode === 'student' && config.readOnly) {
            setAlert('Feuille corrigée — tu peux consulter les annotations du professeur.', 'info');
        } else if (config.mode === 'teacher') {
            setAlert('Mode correction : dessine à l\'encre rouge sur la feuille de l\'élève.', 'info');
        }

        bindEvents();
        await goToPage(0);
        setStatus(state.readOnly ? 'Consultation' : 'Prêt');
    }

    boot().catch(() => setStatus('Impossible de charger le PDF', 'error'));

    const api = {
        getDocument: () => state.document,
        save: saveAnnotations,
    };
    root._worksheetApi = api;

    return api;
}

window.initWorksheetViewer = initWorksheetViewer;
