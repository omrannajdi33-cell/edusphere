import './bootstrap';
import './worksheet-viewer';
import Alpine from 'alpinejs';
import { registerCompetencyModules } from './modules';

window.Alpine = Alpine;
registerCompetencyModules(Alpine);
Alpine.start();
