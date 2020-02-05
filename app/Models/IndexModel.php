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
            'url' => 'nullable'
        ]);
        if ($validator->fails()) {
            return ['status' => 400, 'errors' => $validator->errors()];
        }
        
        /**
         * Process file
         */
        return $this->lineByLineFileReader->process();
    }
}

