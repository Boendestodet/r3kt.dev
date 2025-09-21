<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\CloudflareService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SubdomainController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private CloudflareService $cloudflareService
    ) {
        //
    }

    /**
     * Check if subdomain is available
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'subdomain' => 'required|string|min:3|max:63'
        ]);

        $subdomain = strtolower($request->subdomain);

        // Validate subdomain format
        if (!Project::isValidSubdomain($subdomain)) {
            return response()->json([
                'available' => false,
                'message' => 'Invalid subdomain format. Use only lowercase letters, numbers, and hyphens.'
            ]);
        }

        $available = Project::isSubdomainAvailable($subdomain);

        return response()->json([
            'available' => $available,
            'message' => $available ? 'Subdomain is available' : 'Subdomain is already taken'
        ]);
    }

    /**
     * Update project subdomain
     */
    public function updateSubdomain(Project $project, Request $request): JsonResponse
    {
        $this->authorize('update', $project);

        $request->validate([
            'subdomain' => 'required|string|min:3|max:63'
        ]);

        $subdomain = strtolower($request->subdomain);

        // Validate subdomain format
        if (!Project::isValidSubdomain($subdomain)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid subdomain format. Use only lowercase letters, numbers, and hyphens.'
            ], 422);
        }

        // Check if subdomain is available (excluding current project)
        if ($subdomain !== $project->subdomain && !Project::isSubdomainAvailable($subdomain)) {
            return response()->json([
                'success' => false,
                'message' => 'Subdomain is already taken'
            ], 422);
        }

        try {
            // If DNS was configured, remove old DNS record
            if ($project->dns_configured && $project->subdomain) {
                $this->removeDnsRecord($project->subdomain);
            }

            // Update project subdomain
            $project->update([
                'subdomain' => $subdomain,
                'dns_configured' => false,
                'preview_url' => $project->getProjectUrl()
            ]);

            // Configure new DNS record if project is deployed
            if ($project->status === 'ready') {
                $this->configureDnsForProject($project);
            }

            return response()->json([
                'success' => true,
                'message' => 'Subdomain updated successfully',
                'subdomain' => $subdomain,
                'url' => $project->getProjectUrl()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update subdomain: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Configure custom domain
     */
    public function configureCustomDomain(Project $project, Request $request): JsonResponse
    {
        $this->authorize('update', $project);

        $request->validate([
            'custom_domain' => 'required|string|min:3|max:255'
        ]);

        $customDomain = strtolower($request->custom_domain);

        try {
            $project->update([
                'custom_domain' => $customDomain,
                'preview_url' => $project->getProjectUrl()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Custom domain configured successfully',
                'custom_domain' => $customDomain,
                'url' => $project->getProjectUrl()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to configure custom domain: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove custom domain
     */
    public function removeCustomDomain(Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        try {
            $project->update([
                'custom_domain' => null,
                'preview_url' => $project->getProjectUrl()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Custom domain removed successfully',
                'url' => $project->getProjectUrl()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove custom domain: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test Cloudflare connection
     */
    public function testCloudflareConnection(): JsonResponse
    {
        $result = $this->cloudflareService->testConnection();

        return response()->json($result);
    }

    /**
     * Configure DNS for project
     */
    private function configureDnsForProject(Project $project): void
    {
        if (!$project->subdomain || $project->dns_configured) {
            return;
        }

        $result = $this->cloudflareService->createDnsRecord($project->subdomain);
        
        if ($result['success']) {
            $project->update([
                'dns_configured' => true,
                'preview_url' => $project->getProjectUrl()
            ]);
        }
    }

    /**
     * Remove DNS record
     */
    private function removeDnsRecord(string $subdomain): void
    {
        $records = $this->cloudflareService->getDnsRecords($subdomain);
        
        if ($records['success']) {
            foreach ($records['records'] as $record) {
                $this->cloudflareService->deleteDnsRecord($record['id']);
            }
        }
    }
}
