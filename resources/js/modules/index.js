import { lectureModule, lectureIslamiqueModule } from './lecture';
import { ecritureEditor } from './ecriture';
import { oralRecorder } from './oral';
import { problemesWorkspace } from './problemes';
import { geometrieBoard } from './geometrie';
import { observationAnnotator } from './observation';
import { experienceLab } from './experience';
import { timelineSorter } from './timeline';
import { carteInteractive } from './carte';
import { checklistProgress, calculModule } from './checklist';
import { examTimer } from './exam-timer';

export function registerCompetencyModules(Alpine) {
    Alpine.data('lectureModule', lectureModule);
    Alpine.data('lectureIslamiqueModule', lectureIslamiqueModule);
    Alpine.data('ecritureEditor', ecritureEditor);
    Alpine.data('oralRecorder', oralRecorder);
    Alpine.data('problemesWorkspace', problemesWorkspace);
    Alpine.data('geometrieBoard', geometrieBoard);
    Alpine.data('observationAnnotator', observationAnnotator);
    Alpine.data('experienceLab', experienceLab);
    Alpine.data('timelineSorter', timelineSorter);
    Alpine.data('carteInteractive', carteInteractive);
    Alpine.data('checklistProgress', checklistProgress);
    Alpine.data('calculModule', calculModule);
    Alpine.data('examTimer', examTimer);
}
