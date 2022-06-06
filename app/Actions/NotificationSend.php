<?php

namespace App\Actions;

use App\Models\NotificationTemplate;
use Lorisleiva\Actions\Concerns\AsAction;

class NotificationSend
{
    use AsAction;

    public ?NotificationTemplate $template = null;

    public function __construct(NotificationTemplate $template = null)
    {
        $this->template = $template;
    }

    public static function withTemplate(NotificationTemplate $template)
    {
        return new static($template);
    }

    public function handle()
    {
        if ($this->template) {
            return $this->sendWithTemplate();
        }
    }

    protected function sendWithTemplate()
    {
        $users = $this->template->options->users();

        $total = $users->count();

        $users->each(function ($user) {
            $user->notify($this->template->options->notification());
        }, 50);

        return [
            'template' => $this->template,
            'total_sent' => $total,
            'channels' => $this->template->options->via(),
        ];
    }
}
