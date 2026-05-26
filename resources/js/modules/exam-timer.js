export function examTimer(config) {
    return {
        formatted: '--:--',
        urgent: false,
        expired: false,

        init() {
            if (!config.durationMinutes) {
                return;
            }

            const endAt = new Date(config.startedAt).getTime() + config.durationMinutes * 60 * 1000;

            const tick = () => {
                const left = Math.max(0, Math.floor((endAt - Date.now()) / 1000));
                const minutes = Math.floor(left / 60);
                const seconds = left % 60;
                this.formatted = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                this.urgent = left > 0 && left <= 300;
                this.expired = left === 0;
            };

            tick();
            setInterval(tick, 1000);
        },
    };
}
