<?php

namespace App\Services;

class AccessibilityService
{
    /**
     * Generate accessible color contrast ratios.
     */
    public function getContrastRatio(string $color1, string $color2): float
    {
        $rgb1 = $this->hexToRgb($color1);
        $rgb2 = $this->hexToRgb($color2);
        
        $luminance1 = $this->getLuminance($rgb1);
        $luminance2 = $this->getLuminance($rgb2);
        
        $lighter = max($luminance1, $luminance2);
        $darker = min($luminance1, $luminance2);
        
        return ($lighter + 0.05) / ($darker + 0.05);
    }

    /**
     * Check if color combination meets WCAG AA standards.
     */
    public function meetsWCAGAA(string $foreground, string $background): bool
    {
        $ratio = $this->getContrastRatio($foreground, $background);
        return $ratio >= 4.5; // WCAG AA standard
    }

    /**
     * Check if color combination meets WCAG AAA standards.
     */
    public function meetsWCAGAAA(string $foreground, string $background): bool
    {
        $ratio = $this->getContrastRatio($foreground, $background);
        return $ratio >= 7; // WCAG AAA standard
    }

    /**
     * Generate accessible color palette.
     */
    public function generateAccessiblePalette(string $baseColor): array
    {
        $rgb = $this->hexToRgb($baseColor);
        
        return [
            'primary' => $baseColor,
            'primary_dark' => $this->darkenColor($rgb, 0.2),
            'primary_light' => $this->lightenColor($rgb, 0.2),
            'text_on_primary' => $this->getAccessibleTextColor($baseColor),
            'background_on_primary' => $this->getAccessibleBackgroundColor($baseColor),
        ];
    }

    /**
     * Get accessible text color for a background.
     */
    public function getAccessibleTextColor(string $backgroundColor): string
    {
        $rgb = $this->hexToRgb($backgroundColor);
        $luminance = $this->getLuminance($rgb);
        
        // If background is dark, use light text; if light, use dark text
        return $luminance > 0.5 ? '#000000' : '#FFFFFF';
    }

    /**
     * Get accessible background color for text.
     */
    public function getAccessibleBackgroundColor(string $textColor): string
    {
        $rgb = $this->hexToRgb($textColor);
        $luminance = $this->getLuminance($rgb);
        
        // If text is dark, use light background; if light, use dark background
        return $luminance > 0.5 ? '#FFFFFF' : '#000000';
    }

    /**
     * Generate ARIA labels for dynamic content.
     */
    public function generateAriaLabel(string $action, string $target, ?string $context = null): string
    {
        $label = ucfirst($action) . ' ' . $target;
        
        if ($context) {
            $label .= ' ' . $context;
        }
        
        return $label;
    }

    /**
     * Generate screen reader friendly status messages.
     */
    public function generateStatusMessage(string $status, string $action, ?string $details = null): string
    {
        $message = "Status: {$status}. Action: {$action}";
        
        if ($details) {
            $message .= ". Details: {$details}";
        }
        
        return $message;
    }

    /**
     * Validate form accessibility.
     */
    public function validateFormAccessibility(array $formData): array
    {
        $issues = [];
        
        foreach ($formData as $field => $data) {
            if (empty($data['label'])) {
                $issues[] = "Field '{$field}' is missing a label";
            }
            
            if (empty($data['description']) && !empty($data['required'])) {
                $issues[] = "Required field '{$field}' should have a description";
            }
            
            if (!empty($data['error']) && empty($data['aria_describedby'])) {
                $issues[] = "Field '{$field}' with error should have aria-describedby";
            }
        }
        
        return $issues;
    }

    /**
     * Convert hex color to RGB array.
     */
    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Calculate relative luminance of a color.
     */
    private function getLuminance(array $rgb): float
    {
        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;
        
        $r = $r <= 0.03928 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = $g <= 0.03928 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = $b <= 0.03928 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);
        
        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    /**
     * Darken a color by a percentage.
     */
    private function darkenColor(array $rgb, float $amount): string
    {
        $rgb['r'] = max(0, $rgb['r'] * (1 - $amount));
        $rgb['g'] = max(0, $rgb['g'] * (1 - $amount));
        $rgb['b'] = max(0, $rgb['b'] * (1 - $amount));
        
        return sprintf('#%02x%02x%02x', $rgb['r'], $rgb['g'], $rgb['b']);
    }

    /**
     * Lighten a color by a percentage.
     */
    private function lightenColor(array $rgb, float $amount): string
    {
        $rgb['r'] = min(255, $rgb['r'] + (255 - $rgb['r']) * $amount);
        $rgb['g'] = min(255, $rgb['g'] + (255 - $rgb['g']) * $amount);
        $rgb['b'] = min(255, $rgb['b'] + (255 - $rgb['b']) * $amount);
        
        return sprintf('#%02x%02x%02x', $rgb['r'], $rgb['g'], $rgb['b']);
    }
}