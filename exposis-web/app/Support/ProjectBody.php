<?php

namespace App\Support;

use Illuminate\Support\Str;

final class ProjectBody
{
    public static function sanitize(string $body): string
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,br,strong,b,em,i,u,ul,ol,li,h2,h3,h4,blockquote,a[href|title]');
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
        $config->set('Cache.DefinitionImpl', null);

        return (new \HTMLPurifier($config))->purify($body);
    }

    public static function render(?string $body): string
    {
        if ($body === null || trim($body) === '') {
            return '';
        }

        if (preg_match('/<\/?(?:p|br|strong|b|em|i|u|ul|ol|li|h[2-4]|blockquote|a)\b/i', $body)) {
            return self::sanitize($body);
        }

        return Str::markdown($body, ['html_input' => 'strip', 'allow_unsafe_links' => false]);
    }
}
