<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MarkdownContentNegotiation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Check if the request accepts text/markdown
        if ($request->prefers(['text/markdown', 'text/html']) === 'text/markdown' || 
            str_contains($request->header('Accept', ''), 'text/markdown')) {
            
            $content = $response->getContent();

            // Intercept HTML responses only
            $contentType = $response->headers->get('Content-Type');
            if ($contentType && str_contains($contentType, 'text/html')) {
                
                // Strip tags/blocks we don't want in markdown
                $content = preg_replace('/<script\b[^>]*>([\s\S]*?)<\/script>/i', '', $content);
                $content = preg_replace('/<style\b[^>]*>([\s\S]*?)<\/style>/i', '', $content);
                $content = preg_replace('/<header\b[^>]*>([\s\S]*?)<\/header>/i', '', $content);
                $content = preg_replace('/<footer\b[^>]*>([\s\S]*?)<\/footer>/i', '', $content);
                $content = preg_replace('/<nav\b[^>]*>([\s\S]*?)<\/nav>/i', '', $content);
                
                // Get body content if possible to filter layouts
                if (preg_match('/<body\b[^>]*>([\s\S]*?)<\/body>/i', $content, $matches)) {
                    $content = $matches[1];
                }

                // Simple HTML to Markdown conversions
                $md = $content;
                $md = preg_replace('/<h1>(.*?)<\/h1>/i', "\n# $1\n\n", $md);
                $md = preg_replace('/<h2>(.*?)<\/h2>/i', "\n## $1\n\n", $md);
                $md = preg_replace('/<h3>(.*?)<\/h3>/i', "\n### $1\n\n", $md);
                $md = preg_replace('/<h4>(.*?)<\/h4>/i', "\n#### $1\n\n", $md);
                $md = preg_replace('/<strong>(.*?)<\/strong>/i', "**$1**", $md);
                $md = preg_replace('/<b>(.*?)<\/b>/i', "**$1**", $md);
                $md = preg_replace('/<em>(.*?)<\/em>/i', "*$1*", $md);
                $md = preg_replace('/<i>(.*?)<\/i>/i', "*$1*", $md);
                $md = preg_replace('/<p>(.*?)<\/p>/i', "$1\n\n", $md);
                $md = preg_replace('/<li>(.*?)<\/li>/i', "- $1\n", $md);
                $md = preg_replace('/<ul>([\s\S]*?)<\/ul>/i', "$1\n", $md);
                $md = preg_replace('/<ol>([\s\S]*?)<\/ol>/i', "$1\n", $md);
                $md = preg_replace('/<br\s*\/?>/i', "\n", $md);
                $md = preg_replace('/<a\s+[^>]*href="([^"]*)"[^>]*>(.*?)<\/a>/i', "[$2]($1)", $md);
                
                // Remove all remaining HTML tags
                $md = preg_replace('/<[^>]+>/', "", $md);
                
                // Decode HTML entities & normalize whitespace spacing
                $md = html_entity_decode($md, ENT_QUOTES, 'UTF-8');
                $md = preg_replace('/\n{3,}/', "\n\n", $md);
                $md = trim($md);

                // Set markdown content and headers
                $response->setContent($md);
                $response->headers->set('Content-Type', 'text/markdown; charset=utf-8');
                
                // Calculate token count and set x-markdown-tokens header (1 token ~ 4 characters in English)
                $tokenEstimate = ceil(strlen($md) / 4);
                $response->headers->set('x-markdown-tokens', $tokenEstimate);
            }
        }

        return $response;
    }
}
