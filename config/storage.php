<?php
// config/storage.php
return [
'local' => [
    'type' => 'Local',
    'root' => storage_path('app'),
],
's3' => [
    'type' => 'AwsS3',
    'driver' => 's3',
    'key' => 'AKIAINGIJTWWNMSGFFJQ',
    'secret' => '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',
    'region' => 'us-west-1',
    'version' => 'latest',
    'bucket' => 'one-potato-backup',
    'root'   => '',
]
]

?>