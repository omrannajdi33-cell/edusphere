@php
    $moduleData = $submission->answers['_module'] ?? [];
    $progressUrl = route('student.activites.progress', $activity);
    $submitUrl = route('student.activites.submit', $activity);
    $backUrl = $backUrl ?? route('student.matieres.show', $activity->competency->subject);
@endphp
