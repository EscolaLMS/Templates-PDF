<?php

namespace EscolaLms\TemplatesPdf\Models;

use EscolaLms\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EscolaLms\TemplatesPdf\Database\Factories\FabricPdfFactory;
use EscolaLms\Templates\Services\Contracts\TemplateServiceContract;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use EscolaLms\Templates\Models\Template;
use EscolaLms\TemplatesPdf\Events\EscolaLmsPdfCreatedEvent;

/**
 * @OA\Schema(
 *      schema="FabricPDF",
 *      @OA\Property(
 *          property="id",
 *          description="template id",
 *          type="integer",
 *      ),
 *      @OA\Property(
 *          property="content",
 *          description="fabric.js serialized content",
 *          type="object"
 *      ),
 *      @OA\Property(
 *          property="path",
 *          description="path to rendered PDF binary file",
 *          type="string"
 *      )
 * )
 */
class FabricPDF extends Model
{
    use HasFactory;

    protected $table = 'fabric_pdfs';

    protected $casts = [
        'id' => 'integer',
        'content' => 'array'
    ];

    protected $guarded = [
        'id'
    ];

    protected static function newFactory()
    {
        return FabricPdfFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    protected static function booted()
    {
        self::created(function (FabricPDF $pdf) {
            event(new EscolaLmsPdfCreatedEvent($pdf->user, $pdf));
        });
    }
}
