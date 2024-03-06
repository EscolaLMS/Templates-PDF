<?php

namespace EscolaLms\TemplatesPdf\Tests\Api;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\TemplatesPdf\Models\FabricPDF;
use EscolaLms\TemplatesPdf\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;

class PdfTest extends TestCase
{
    use DatabaseTransactions;
    use CreatesUsers;

    public function setUp(): void
    {
        parent::setUp();
        $this->user =  $this->makeStudent();
        $this->user2 =  $this->makeStudent();
        $this->admin =  $this->makeAdmin();
    }

    public function testCanReadExisting(): void
    {
        $pdf = FabricPDF::factory()->createOne(
            [
                'user_id' => $this->user->id,
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
                'vars'
            ],
            'message'
        ]);

        $response =  $this->actingAs($this->user2)
            ->getJson('/api/pdfs/' . $pdf->id);

        $response->assertStatus(403);
    }

    public function testCanReadWithVars(): void
    {
        $pdf = FabricPDF::factory()->createOne(
            [
                'user_id' => $this->user->id,
                'vars' => [
                    '@GlobalSettingsValue' => 'value_1',
                    '@VarExampleValue' => 'value_2'
                ]
            ]
        );

        $this->actingAs($this->user)
            ->getJson('/api/pdfs/' . $pdf->id)
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'content',
                    'created_at',
                    'id',
                    'path',
                    'template',
                    'vars'
                ],
                'message'
            ])
            ->assertJsonFragment([
                'vars' => [
                    'var_example_value' => 'value_2'
                ]
            ]);
    }

    public function testAdminCanReadAny(): void
    {
        $pdf = FabricPDF::factory()->createOne(
            [
                'user_id' => $this->user->id
            ]
        );

        $response =  $this->actingAs($this->admin)
            ->getJson('/api/pdfs/' . $pdf->id);

        $response->assertOk();
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


    public function testListExistingAssignable(): void
    {
        $pdf1 = FabricPDF::factory()->createOne(
            [
                'user_id' => $this->user->id,
                'assignable_type' => 'EscolaLms\Test\Models\Test',
                'assignable_id' => '1'
            ]
        );
        $pdf2 = FabricPDF::factory()->createOne(
            [
                'user_id' => $this->user->id,
            ]
        );

        $response =  $this->actingAs($this->user)
            ->getJson('/api/pdfs?assignable_type=EscolaLms\Test\Models\Test&assignable_id=1');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');

        $seek = false;

        foreach ($response->getData()->data as $item) {
            if ($item->id == $pdf1->id) {
                $seek = $pdf1;
            }
        }

        $this->assertEquals($pdf1->id, $seek->id);
    }

    public function testListPdfsPagination(): void
    {
        FabricPDF::factory()
            ->state([
                'user_id' => $this->user->id,
            ])
            ->count(35)
            ->create();

        $this->actingAs($this->user, 'api')
            ->getJson('api/pdfs?per_page=10')
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJson([
                'meta' => [
                    'total' => 35
                ]
            ]);

        $this->actingAs($this->user, 'api')
            ->getJson('api/pdfs?per_page=10&page=4')
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJson([
                'meta' => [
                    'total' => 35
                ]
            ]);
    }
        public function testListAdmin(): void
    {
        $pdf = FabricPDF::factory()->createOne(
            [
                'user_id' => $this->user->id
            ]
        );

        $admin = $this->makeAdmin();
        $response =  $this->actingAs($admin, 'api')->getJson('/api/admin/pdfs');

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
