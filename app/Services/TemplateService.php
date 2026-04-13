<?php

namespace App\Services;

class TemplateService
{
    public function render(string $body, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $body = str_replace("{{{$key}}}", $value, $body);
        }

        return $body;
    }

    public function renderSubject(string $subject, array $variables): string
    {
        return $this->render($subject, $variables);
    }

    public function extractVariables(string $body): array
    {
        preg_match_all('/\{\{(\w+)\}\}/', $body, $matches);
        return $matches[1] ?? [];
    }
}
