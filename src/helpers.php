<?php

use Utyemma\LaraNotice\Notify;

if(!function_exists('notify')) {
    function notify($subject, $data = []){
        return new Notify($subject, $data);
    }
}
