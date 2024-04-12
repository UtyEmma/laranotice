<?php

namespace Utyemma\LaraNotice;

use Utyemma\LaraNotice\Models\Mailable as UtyEmmaMailable;

abstract class Mailable extends Notify {

    function __construct() {
        $class = get_class($this);
        $model = Mailable::class;

        if($this->source == 'database') {
            if(!$this->mailable = UtyEmmaMailable::whereMailable(get_class($this))->first()) {
                throw new \Exception("Database mailable [{$class}] does not exist on $model model");
            }

            return $this->parse($this->mailable);
        }

        if($this->source == 'inline') {
            $content = $this->setBody();
            $contentType = gettype($content);
            if($contentType != 'string') {
                throw new \Exception("Method [$class::setBody()] must return a [{string}]. $contentType returned");
            }

            return $this->parse([
                'subject' => $this->subject,
                'body' => $content,
            ]);
        }
    }

}
