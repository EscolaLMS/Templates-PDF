<?php

namespace EscolaLms\TemplatesPdf\Core;

use Illuminate\Mail\Mailable;

class PdfMailable extends Mailable
{
    public function getHtml(): ?string
    {
        return $this->html ?? null;
    }

    public function build()
    {
    }
}
