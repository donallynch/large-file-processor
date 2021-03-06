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
        /* Validate request */
        $validator = Validator::make($request->post(), [
            'url' => 'required|url'
        ]);
        if ($validator->fails()) {
            exit("400_bad_request_url_parameter_{$validator->errors()->get('url')[0]}");
        }

        /* Retrieve URL from get parameter */
        $url = $request->get('url');

        /* Ensure URL parameter was provided in the URL */
        if ($url === null) {
            exit('400-no-url-parameter-provided-add-parameter-to-url-and-try-again');
        }
        
        /**
         * Process file and return result
         */
        return $this->lineByLineFileReader
            ->process($url)
            ->getResult();
    }
}

