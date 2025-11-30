<?php declare(strict_types=1);

return [
    'report' => [
        'subject' => 'API Test durchgeführt [:name]',
        'intro' => 'Die API Tests :name wurden ausgeführt. Die Resultate können der Tabelle entnommen werden:',
        'no_results' => 'Keine Daten/Erwartungen vorhanden.',
        'action' => [
            'view' => 'Anzeigen',
        ],
        'table' => [
            'assertion' => 'Test',
            'actual_value' => 'Erhaltener Wert',
            'result' => 'Ergebnis',
            'failed' => 'Fehler',
            'success' => 'Erfolg',
        ],
        'thanks' => 'Danke fürs nutzen der App,',
    ],

    'failed' => [
        'subject' => 'API Test Fehler [:name]',
        'intro' => 'Die API Tests :name konnten nicht erfolgreich ausgeführt werden (:error).',
        'outro' => 'Ausführungen dieser Tests müssen manuell reaktiviert werden.',
        'action' => [
            'view' => 'Anzeigen',
        ],
    ],
];
