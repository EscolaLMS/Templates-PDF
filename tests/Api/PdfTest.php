<?php

namespace EscolaLms\TemplatesPdf\Tests\Api;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\TemplatesPdf\Models\FabricPDF;
use EscolaLms\TemplatesPdf\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PdfTest extends TestCase
{
    use DatabaseTransactions;
    use CreatesUsers;


    public function setUp(): void
    {
        parent::setUp();
        $this->user =  $this->makeStudent();
        $this->user2 =  $this->makeStudent();
    }


    public function testCanReadExisting(): void
    {
        $pdf = FabricPDF::factory()->createOne(
            [
                'user_id' => $this->user->id
            ]
        );

        $response =  $this->actingAs($this->user)
            ->getJson('/api/pdfs/' . $pdf->id);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'content',
                'created_at',
                'id',
                'path',
                'template',
            ],
            'message'
        ]);

        $response =  $this->actingAs($this->user2)
            ->getJson('/api/pdfs/' . $pdf->id);

        $response->assertStatus(403);
    }

    public function testListExisting(): void
    {
        $pdf = FabricPDF::factory()->createOne(
            [
                'user_id' => $this->user->id
            ]
        );

        $response =  $this->actingAs($this->user)
            ->getJson('/api/pdfs');

        $response->assertOk();

        $seek = false;

        foreach ($response->getData()->data as $item) {
            if ($item->id == $pdf->id) {
                $seek = $pdf;
            }
        }

        $this->assertEquals($pdf->id, $seek->id);
    }

    public function testCannotListAsGuest(): void
    {
        $response =  $this->getJson('/api/pdfs');

        $response->assertStatus(403);
    }


    public function testCannotFindMissing(): void
    {
        $response = $this->actingAs($this->user2)
            ->getJson('/api/pdfs/' . 9999999);
        $response->assertNotFound();
    }
}
