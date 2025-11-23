<?php declare(strict_types=1);

return [
    'report' => [
        'subject' => 'API Test conducted [:name]',
        'intro' => 'The api suite :name was executed. The results can be found in the table below:',
        'no_results' => 'No Data/Assertions are set.',
        'action' => [
            'view' => 'View',
        ],
        'table' => [
            'assertion' => 'Assertion',
            'actual_value' => 'Value',
            'result' => 'Result',
            'failed' => 'Failed',
            'success' => 'Success',
        ],
        'thanks' => 'Thanks,',
    ],

    'failed' => [
        'subject' => 'API Test error [:name]',
        'intro' => 'The api suite :name has run into an error (:error).',
        'outro' => 'Runs of this suite will be paused until manually reactivated',
        'action' => [
            'view' => 'View',
        ],
    ],
];
