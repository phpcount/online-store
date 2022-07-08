<?php

namespace App\Utils\Helpers;

use Symfony\Component\HttpFoundation\Request;

class JsonHandler
{
    public static function parseJson(Request $request, $toArray = false)
    {
        $content = $request->getContent();
        
        if (!$content || $request->getContentType() !== 'json') {
            return null;
        }

        return json_decode($content, $toArray);
    }
}