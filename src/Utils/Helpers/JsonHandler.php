<?php

namespace App\Utils\Helpers;

use Symfony\Component\HttpFoundation\Request;

class JsonHandler
{
    public static function parseJson(Request $request, $toArray = false)
    {
        $content = $request->getContent();

        if (!$content || 'json' !== $request->getContentType()) {
            return null;
        }

        return json_decode($content, $toArray);
    }
}
