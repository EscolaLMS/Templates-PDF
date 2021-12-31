<?php

namespace EscolaLms\TemplatesPdf\Tests\Api;

use EscolaLms\TemplatesPdf\Models\FabricPDF;
use EscolaLms\TemplatesPdf\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use EscolaLms\Core\Tests\CreatesUsers;

class PdfTests extends TestCase
{
    use DatabaseTransactions;
    use CreatesUsers;


    public function setUp(): void
    {
        parent::setUp();
        $this->user =  $this->makeStudent();
        $this->user2 =  $this->makeStudent();
    }


    public function testCanReadExisting()
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

    public function testListExisting()
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

    public function testCannotListAsGuest()
    {
        $response =  $this->getJson('/api/pdfs');
<<<<<<< HEAD
=======

>>>>>>> c5a740f9289f4ad320f161bcb238828cf0532932
        $response->assertStatus(403);
    }


    public function testCannotFindMissing()
    {
        $response = $this->actingAs($this->user2)
            ->getJson('/api/pdfs/' . 9999999);
        $response->assertNotFound();
    }

    /*
    public function testAdminCanReadExistingById()
    {
        $this->authenticateAsAdmin();

        $page = Page::factory()->createOne();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/admin/pages/' . $page->getKey());
        $response->assertOk();
        $response->assertJsonFragment(collect($page->getAttributes())->except('id', 'slug')->toArray());
    }

    */
}
