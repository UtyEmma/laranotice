<?php

namespace Utyemma\LaraNotice;

use Exception;
use Illuminate\Mail\Mailable as LaravelMailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Illuminate\Support\HtmlString;
use Mustache_Engine;
use Utyemma\LaraNotice\Models\Mailable;
use Utyemma\LaraNotice\Notifications\Notification;

class Notify extends MailMessage {

    public $content = [];
    private Mailable | null $mailable = null;
    protected $source;

    function setBody() { }

    function __construct($subject = '', $data = []) {
        $this->content = $data;

        $class = get_class($this);
        $model = Mailable::class;

        $this->subject($subject);

        if($this->source == 'database') {
            if(!$this->mailable = Mailable::whereMailable(get_class($this))->first()) {
                throw new Exception("Database mailable [{$class}] does not exist on $model model");
            }

            return $this->parse($this->mailable);
        }

        if($this->source == 'inline') {
            $content = $this->setBody();
            $contentType = gettype($content);
            if(!$contentType != 'string') {
                throw new Exception("Method [$class::setBody()] must return a [{string}]. $contentType returned");
            }

            return $this->parse([
                'subject' => $this->subject,
                'body' => $content,
            ]);
        }
    }

    function send($receivers, $channels = null){
        if(is_string($receivers)) return $this->mail($receivers);
        LaravelNotification::send($receivers, new Notification($channels, $this));
        $this->record();
    }

    function sendNow($receivers, $channels = null){
        if(is_string($receivers)) return $this->mail($receivers);
        LaravelNotification::sendNow($receivers, new Notification($channels, $this));
        $this->record();
    }

    private function mailable(){
        $mailable = new LaravelMailable();
        $mailable->subject = $this->subject;
        return $mailable->html($this->render()->toHtml());
    }

    function mail($email){
        $this->record();
        return Mail::to($email)->send($this->mailable());
    }

    private function parse($data){
        $this->subject($data['subject']);
        $this->greeting(' ');
        $this->salutation(' ');
        $text = preg_replace('/(["\']{3,})/', '"', $data['body']);
        $message = $this->resolver(trim($text), $data);
        $this->line(new HtmlString($message));
        return $this;
    }

    private function resolver($content, $data){
        if($resolver = config('laranotice.resolver')) return new $resolver($content, $data);
        return $this->setResolver($content, $data);
    }

    protected function setResolver($content, $data){
        return (new Mustache_Engine)->render(trim($content), $data);
    }

    function record(){
        if($this->mailable) {
            ++$this->mailable->sent;
            $this->mailable->save();
        }
    }

}
