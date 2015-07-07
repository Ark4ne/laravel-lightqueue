<?php
return [
    'ligthqueue' => [
        "driver" => "file",
        "processes" => [
            "max_by_queue" => 4
        ],
        "queue_directory" => __DIR__ . '\\..\\queue\\'
    ],
];