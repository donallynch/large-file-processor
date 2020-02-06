<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

/**
 * Class IndexTest
 * @package Tests\Feature
 */
class IndexTest extends TestCase
{
    /**
     * @return void
     */
    public function testBadGetMissingUrlParameter()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/');
        $response->assertStatus(400);
    }

    /**
     * @return void
     */
    public function testSuccessfulGetToIndexPage()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/?url=https://s3.amazonaws.com/swrve-public/full_stack_programming_test/test_data.csv.gz');
        $response->assertStatus(200);
    }
}
