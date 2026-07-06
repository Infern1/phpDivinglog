<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final readonly class TwigRenderer
{
    private Environment $twig;

    /**
     * @param array<string, mixed> $globals
     */
    public function __construct(string $templatesPath, string $cachePath, array $globals = [])
    {
        $loader = new FilesystemLoader($templatesPath);
        $this->twig = new Environment($loader, [
            'cache' => $cachePath,
            'auto_reload' => true,
            'autoescape' => 'html',
        ]);

        foreach ($globals as $name => $value) {
            $this->twig->addGlobal($name, $value);
        }
    }

    /**
     * @param array<string, mixed> $context
     */
    public function render(string $template, array $context = []): string
    {
        return $this->twig->render($template, $context);
    }
}
