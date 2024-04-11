<?php

return [
    
    'source' => 'database', //database,inline
    
    'resolver' => null,

    'table' => 'mailables',

    'defaults' => [
        'subject' => 'New Email Message',
        'body' => 'The email content goes here...'
    ]

];