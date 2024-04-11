<?php

namespace Utyemma\LaraNotice\Commands;

use Illuminate\Console\GeneratorCommand;
use Utyemma\LaraNotice\Models\Mailable;

class CreateMailable extends GeneratorCommand {

    private $source, $subject, $message;

    protected $signature = 'make:mailable
                    {name : The name of the mailable class}
                    {--source=database : The content source database | inline }
                    {--subject : The subject of the email message }
                ';

    protected $description = 'Create a new Mailable Class';

    protected $type = 'Mailable';

    protected function getStub(){
        $this->source = $this->option('source') ?? config('laranotice.source');
        if($this->source == 'inline') return __DIR__.'/../../stubs/laranotice-inline.stub';
        if($this->source == 'database') return __DIR__.'/../../stubs/laranotice-database.stub';
    }

    protected function getDefaultNamespace($rootNamespace) {
        return $rootNamespace.'\Mailables';
    }

    protected function buildClass($name) {
        $this->subject = $this->option('subject') ? $this->option('subject') : config('laranotice.defaults.subject');
        $this->message = config('laranotice.defaults.body');
        $this->createMailable($name);

        return str_replace(['{{subject}}', "{{message}}"], [$this->subject, $this->message], parent::buildClass($name));
    }

    function createMailable($name){
        $source = $this->option('source');
        $this->subject = $this->option('subject') ? $this->option('subject') : config('laranotice.defaults.subject');
        $this->message = config('laranotice.defaults.body');

        if($source == 'database') {
            Mailable::create([
                'mailable' => $name,
                'subject' => $this->subject,
                'content' => $this->message
            ]);
        }
    }

}
