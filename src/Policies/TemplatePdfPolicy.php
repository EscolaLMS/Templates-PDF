<?php

namespace EscolaLms\TemplatesPdf\Policies;

use EscolaLms\Core\Models\User;
use EscolaLms\TemplatesPdf\Enums\PdfPermissionsEnum;
use EscolaLms\TemplatesPdf\Models\FabricPDF;
use Illuminate\Auth\Access\HandlesAuthorization;

class TemplatePdfPolicy
{
    use HandlesAuthorization;

    public function read(User $user, FabricPDF $pdf): bool
    {
        return $pdf->user_id === $user->id || $user->can(PdfPermissionsEnum::PDF_READ_ALL);
    }

    public function list(?User $user): bool
    {
        return !is_null($user);
    }
}
