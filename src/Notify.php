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
    protected Mailable | null $mailable = null;
    protected $source;

    function setBody() { }

    function toDatabase() {
        return [];
     }

    function __construct($subject = '', $data = []) {
        $this->content = $data;
        $this->subject($subject);
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

    protected function parse($data){
        $this->subject($data['subject']);
        $this->greeting(' ');
        $this->salutation(' ');
        $text = preg_replace('/(["\']{3,})/', '"', $data['body']);
        $message = $this->resolver(trim($text), $data);
        $this->line(new HtmlString($message));
        return $this;
    }

    protected function resolver($content, $data){
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
