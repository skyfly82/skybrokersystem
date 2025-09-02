<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AiContentController extends Controller
{
    public function generateSeoContent(Request $request): JsonResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content_type' => ['required', 'string', 'in:meta_description,keywords,content_outline']
        ]);

        $title = $request->title;
        $contentType = $request->content_type;

        try {
            $generatedContent = $this->generateContent($title, $contentType);
            
            return response()->json([
                'success' => true,
                'content' => $generatedContent,
                'type' => $contentType
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateBannerContent(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'string', 'in:info,warning,error,success'],
            'context' => ['required', 'string', 'max:500'],
            'length' => ['nullable', 'string', 'in:short,medium,long']
        ]);

        $type = $request->type;
        $context = $request->context;
        $length = $request->length ?? 'medium';

        try {
            $suggestions = $this->generateBannerSuggestions($type, $context, $length);
            
            return response()->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate banner content: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateContent(string $title, string $contentType): string
    {
        // Mock AI content generation - in a real implementation, you would integrate with OpenAI API, Claude API, etc.
        switch ($contentType) {
            case 'meta_description':
                return $this->generateMetaDescription($title);
            case 'keywords':
                return $this->generateKeywords($title);
            case 'content_outline':
                return $this->generateContentOutline($title);
            default:
                throw new \InvalidArgumentException('Unknown content type');
        }
    }

    private function generateMetaDescription(string $title): string
    {
        $templates = [
            "Learn about {title} with our comprehensive guide. Expert insights, practical tips, and detailed information to help you succeed.",
            "Discover everything you need to know about {title}. Professional solutions, expert advice, and proven strategies.",
            "Expert guide to {title}. Get professional insights, best practices, and actionable advice from industry experts.",
            "Complete {title} resource. Professional guidance, expert tips, and comprehensive information for success.",
        ];
        
        $template = $templates[array_rand($templates)];
        $description = str_replace('{title}', strtolower($title), $template);
        
        return Str::limit($description, 157); // Leave room for ...
    }

    private function generateKeywords(string $title): string
    {
        $baseKeywords = explode(' ', strtolower($title));
        $industryKeywords = ['professional', 'expert', 'guide', 'tips', 'solutions', 'services', 'consultation', 'advice'];
        $locationKeywords = ['poland', 'warsaw', 'krakow', 'gdansk', 'poznan'];
        
        $keywords = array_merge(
            $baseKeywords,
            array_slice($industryKeywords, 0, 3),
            array_slice($locationKeywords, 0, 2)
        );
        
        return implode(', ', array_unique($keywords));
    }

    private function generateContentOutline(string $title): string
    {
        $outline = [
            "# {$title}",
            "",
            "## Introduction",
            "Brief overview of {$title} and its importance.",
            "",
            "## Key Benefits",
            "- Benefit 1: Efficiency and cost-effectiveness",
            "- Benefit 2: Professional expertise",
            "- Benefit 3: Reliable solutions",
            "",
            "## How It Works",
            "Step-by-step explanation of our {$title} process.",
            "",
            "## Why Choose Our Services",
            "What sets us apart in {$title} delivery.",
            "",
            "## Getting Started",
            "Easy steps to begin with our {$title} services.",
            "",
            "## Frequently Asked Questions",
            "Common questions about {$title} answered.",
            "",
            "## Contact Information",
            "Get in touch for more information about {$title}."
        ];
        
        return implode("\n", $outline);
    }

    private function generateBannerSuggestions(string $type, string $context, string $length): array
    {
        $suggestions = [];
        
        switch ($type) {
            case 'info':
                $suggestions = $this->getInfoBannerSuggestions($context, $length);
                break;
            case 'warning':
                $suggestions = $this->getWarningBannerSuggestions($context, $length);
                break;
            case 'error':
                $suggestions = $this->getErrorBannerSuggestions($context, $length);
                break;
            case 'success':
                $suggestions = $this->getSuccessBannerSuggestions($context, $length);
                break;
        }
        
        return $suggestions;
    }

    private function getInfoBannerSuggestions(string $context, string $length): array
    {
        $templates = [
            'short' => [
                "New: {context}",
                "Info: {context}",
                "Update: {context}",
            ],
            'medium' => [
                "We're excited to announce {context}. Learn more about these updates.",
                "Important information regarding {context}. Please review the details.",
                "New feature available: {context}. Check it out now.",
            ],
            'long' => [
                "We're pleased to inform you about {context}. This update brings improved functionality and enhanced user experience. Please take a moment to review the changes.",
                "Important announcement: {context}. We've made these improvements based on your feedback and to better serve your needs. Visit our help center for more information.",
            ]
        ];
        
        return $this->fillTemplates($templates[$length], $context);
    }

    private function getWarningBannerSuggestions(string $context, string $length): array
    {
        $templates = [
            'short' => [
                "Warning: {context}",
                "Attention: {context}",
                "Important: {context}",
            ],
            'medium' => [
                "Please be aware: {context}. Take necessary precautions.",
                "Important notice regarding {context}. Action may be required.",
                "Attention: {context}. Please review this information carefully.",
            ],
            'long' => [
                "Important warning about {context}. We recommend taking immediate action to avoid any potential issues. Please contact support if you need assistance.",
                "Please pay attention to the following: {context}. This may affect your service experience. We're working to resolve this and will keep you updated.",
            ]
        ];
        
        return $this->fillTemplates($templates[$length], $context);
    }

    private function getErrorBannerSuggestions(string $context, string $length): array
    {
        $templates = [
            'short' => [
                "Error: {context}",
                "Issue: {context}",
                "Problem: {context}",
            ],
            'medium' => [
                "Service disruption: {context}. We're working to resolve this.",
                "Technical issue: {context}. Our team is investigating.",
                "Current problem: {context}. We apologize for any inconvenience.",
            ],
            'long' => [
                "We're currently experiencing technical difficulties with {context}. Our engineering team is actively working to resolve this issue. We apologize for any inconvenience and will provide updates as soon as possible.",
                "Service alert: We're having issues with {context}. This may affect your experience. Our technical team has been notified and is working on a solution. Thank you for your patience.",
            ]
        ];
        
        return $this->fillTemplates($templates[$length], $context);
    }

    private function getSuccessBannerSuggestions(string $context, string $length): array
    {
        $templates = [
            'short' => [
                "Success: {context}",
                "Complete: {context}",
                "Done: {context}",
            ],
            'medium' => [
                "Great news: {context} has been completed successfully!",
                "Success! {context} is now available and ready to use.",
                "We're happy to announce: {context} has been implemented.",
            ],
            'long' => [
                "Excellent news! {context} has been successfully completed. All systems are running smoothly and you can now enjoy the improved functionality. Thank you for your patience during the implementation.",
                "We're thrilled to announce that {context} is now live! This improvement enhances your experience and provides better service. Explore the new features and let us know what you think.",
            ]
        ];
        
        return $this->fillTemplates($templates[$length], $context);
    }

    private function fillTemplates(array $templates, string $context): array
    {
        return array_map(function($template) use ($context) {
            return str_replace('{context}', $context, $template);
        }, $templates);
    }
}
