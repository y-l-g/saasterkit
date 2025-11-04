<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class PrivacyController extends Controller
{
    public function __construct(private MarkdownRenderer $markdownRenderer) {}

    public function __invoke(): Response
    {
        $markdownContent = File::get(resource_path('markdown/privacy.md'));

        $htmlContent = $this->markdownRenderer->toHtml($markdownContent);

        return Inertia::render('public/Privacy', [
            'content' => $htmlContent,
        ]);
    }
}
