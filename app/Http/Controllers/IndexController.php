<?php

namespace App\Http\Controllers;

use App\Models\IndexModel;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Illuminate\Http\Request;

/**
 * Class IndexController
 * @package App\Http\Controllers
 */
class IndexController
{
    /** @var IndexModel $indexModel */
    private $indexModel;

    /**
     * IndexController constructor.
     * @param Request $request
     * @param IndexModel $indexModel
     */
    public function __construct(
        Request $request,
        IndexModel $indexModel
    ){
        $this->indexModel = $indexModel;
    }

    /**
     * @return Factory|View
     */
    public function indexAction(Request $request)
    {
        $result = $this->indexModel->index($request);

        /**
         * Dump results
         */
        echo'<pre>';
        var_dump($result);
        echo'</pre>';
        exit(0);
    }
}

