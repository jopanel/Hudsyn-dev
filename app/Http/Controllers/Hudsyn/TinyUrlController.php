<?php

namespace App\Http\Controllers\Hudsyn;

use App\Http\Controllers\Controller;
use App\Hudsyn\TinyUrl;
use Illuminate\Support\Facades\Redirect;

class TinyUrlController extends Controller
{
    /**
     * Look up the tiny URL by short code and redirect to the original URL.
     *
     * @param string $short_code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect($short_code)
    {
        $tinyUrl = TinyUrl::where('short_code', $short_code)->first();

        if ($tinyUrl) {
            return Redirect::to($tinyUrl->original_url);
        }

        abort(404, 'Tiny URL not found.');
    }
}
