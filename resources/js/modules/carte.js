import { moduleAutosave } from './shared';

export function carteInteractive(config) {
    const saved = config.saved ?? {};

    return {
        markers: saved.markers ?? [],
        links: saved.links ?? [],
        selectedRegion: saved.selectedRegion ?? null,
        placingLabel: '',
        linkMode: false,
        linkFrom: null,
        regions: config.regions ?? [
            { id: 'nord', label: 'Nord', x: 45, y: 15 },
            { id: 'ouest', label: 'Ouest', x: 15, y: 45 },
            { id: 'est', label: 'Est', x: 75, y: 45 },
            { id: 'sud', label: 'Sud', x: 45, y: 75 },
            { id: 'centre', label: 'Centre', x: 45, y: 45 },
        ],
        ...moduleAutosave({
            url: config.progressUrl,
            csrf: config.csrf,
            getPayload() {
                return {
                    markers: this.markers,
                    links: this.links,
                    selectedRegion: this.selectedRegion,
                };
            },
        }),

        selectRegion(region) {
            this.selectedRegion = region.id;

            if (this.placingLabel.trim()) {
                this.markers.push({
                    id: Date.now(),
                    regionId: region.id,
                    label: this.placingLabel.trim(),
                    x: region.x,
                    y: region.y,
                });
                this.placingLabel = '';
            }

            this.scheduleSave();
        },

        toggleLinkMode() {
            this.linkMode = !this.linkMode;
            this.linkFrom = null;
        },

        linkRegion(region) {
            if (!this.linkMode) {
                return;
            }

            if (!this.linkFrom) {
                this.linkFrom = region.id;
                return;
            }

            if (this.linkFrom !== region.id) {
                this.links.push({ from: this.linkFrom, to: region.id });
            }

            this.linkFrom = null;
            this.scheduleSave();
        },

        removeMarker(id) {
            this.markers = this.markers.filter((marker) => marker.id !== id);
            this.scheduleSave();
        },
    };
}
