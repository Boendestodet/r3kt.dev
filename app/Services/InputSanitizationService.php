<?php

namespace App\Services;

class InputSanitizationService
{
    /**
     * Sanitize HTML content by removing dangerous tags and attributes.
     */
    public function sanitizeHtml(string $html): string
    {
        // Remove script tags and their content
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        
        // Remove javascript: protocols
        $html = preg_replace('/javascript:/i', '', $html);
        
        // Remove on* event handlers
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        
        // Remove dangerous tags
        $dangerousTags = ['iframe', 'object', 'embed', 'form', 'input', 'textarea', 'select', 'button'];
        foreach ($dangerousTags as $tag) {
            $html = preg_replace('/<' . $tag . '\b[^>]*>.*?<\/' . $tag . '>/is', '', $html);
            $html = preg_replace('/<' . $tag . '\b[^>]*\/>/is', '', $html);
        }
        
        return trim($html);
    }

    /**
     * Sanitize plain text by removing control characters and limiting length.
     */
    public function sanitizeText(string $text, int $maxLength = 1000): string
    {
        // Remove control characters except newlines and tabs
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // Trim whitespace
        $text = trim($text);
        
        // Limit length
        if (strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength);
        }
        
        return $text;
    }

    /**
     * Sanitize file name by removing dangerous characters.
     */
    public function sanitizeFileName(string $filename): string
    {
        // Remove path traversal attempts
        $filename = str_replace(['../', '..\\', '/', '\\'], '', $filename);
        
        // Remove control characters
        $filename = preg_replace('/[\x00-\x1F\x7F]/', '', $filename);
        
        // Remove dangerous characters
        $filename = preg_replace('/[<>:"|?*]/', '', $filename);
        
        // Limit length
        if (strlen($filename) > 255) {
            $filename = substr($filename, 0, 255);
        }
        
        return trim($filename);
    }

    /**
     * Sanitize URL by validating and cleaning it.
     */
    public function sanitizeUrl(string $url): ?string
    {
        // Remove whitespace
        $url = trim($url);
        
        // Add protocol if missing
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }
        
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }
        
        // Only allow http and https protocols
        $parsed = parse_url($url);
        if (!in_array($parsed['scheme'] ?? '', ['http', 'https'])) {
            return null;
        }
        
        return $url;
    }

    /**
     * Sanitize array of data recursively.
     */
    public function sanitizeArray(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            $sanitizedKey = $this->sanitizeText($key, 100);
            
            if (is_string($value)) {
                $sanitized[$sanitizedKey] = $this->sanitizeText($value);
            } elseif (is_array($value)) {
                $sanitized[$sanitizedKey] = $this->sanitizeArray($value);
            } else {
                $sanitized[$sanitizedKey] = $value;
            }
        }
        
        return $sanitized;
    }

    /**
     * Validate and sanitize project name.
     */
    public function sanitizeProjectName(string $name): string
    {
        // Remove HTML tags
        $name = strip_tags($name);
        
        // Remove control characters
        $name = preg_replace('/[\x00-\x1F\x7F]/', '', $name);
        
        // Only allow alphanumeric, spaces, hyphens, underscores
        $name = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $name);
        
        // Trim and limit length
        $name = trim($name);
        if (strlen($name) > 255) {
            $name = substr($name, 0, 255);
        }
        
        return $name;
    }

    /**
     * Validate and sanitize AI prompt.
     */
    public function sanitizePrompt(string $prompt): string
    {
        // Remove HTML tags
        $prompt = strip_tags($prompt);
        
        // Remove control characters except newlines
        $prompt = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $prompt);
        
        // Limit length to prevent abuse
        if (strlen($prompt) > 10000) {
            $prompt = substr($prompt, 0, 10000);
        }
        
        return trim($prompt);
    }
}