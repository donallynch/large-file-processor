<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

/**
 * Class InviteTest
 * @package Tests\Feature
 */
class InviteTest extends TestCase
{
    /**
     * @return void
     */
    public function testSuccessfulGetToIndexPage()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/index');
        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function testSuccessfulPostToInvite()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('/invite', [
            'markers' => [
                [
                    'lat' => 52.986375,
                    'lng' => 52.986375,
                    'name' => 52.986375,
                    'user_id' => 52.986375,
                ]
            ],
        ]);
        $response->assertStatus(200)->assertJsonFragment(['status' => 200]);
    }

    /**
     * @return void
     */
    public function testBadPostToInviteWrongType()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('/invite', [['markers' => 'bad-parameter-type']]);
        $response->assertJsonFragment(['status' => 400]);
    }

    /**
     * @return void
     */
    public function testBadPostToInviteNoMarkersArray()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('/invite', []);
        $response->assertJsonFragment(['status' => 400]);
    }
}
