<?php

namespace App\Models;

use App\Services\LineByLineFileReader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class IndexModel
 * @package App\Models
 */
class IndexModel
{
    /** @var LineByLineFileReader $lineByLineFileReader */
    private $lineByLineFileReader;

    /**
     * IndexModel constructor.
     */
    public function __construct(
        LineByLineFileReader $lineByLineFileReader
    ){
        $this->lineByLineFileReader = $lineByLineFileReader;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function index(Request $request)
    {
        /* Validate */
        $validator = Validator::make($request->post(), [
            'url' => 'required|url'
        ]);
        if ($validator->fails()) {
            return ['status' => 400, 'errors' => $validator->errors()];
        }

        /* Retrieve URL from get parameter */
        $url = $request->get('url');

        /**
         * Ensure URL parameter was provided in the URL
         */
        if ($url === null) {
            exit('400-no-url-parameter-provided-add-parameter-to-url-and-try-again');
        }
        
        /**
         * Process file
         */
        return $this->lineByLineFileReader->process($url);
    }
}

