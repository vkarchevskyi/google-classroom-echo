<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\Classroom\GoogleAuthService;
use Google\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class GoogleAuthController extends AbstractController
{
    #[Route('/', name: 'app_google_auth')]
    public function index(
        Request $request,
        Client $client,
        GoogleAuthService $googleAuthService
    ): JsonResponse|RedirectResponse {
        $code = $request->get('code');
        $client = $googleAuthService->run($client, $code);

        if (is_string($client)) {
            return $this->redirect($client);
        }

        return $this->json([
            'message' => 'You have been successfully authenticated!'
        ]);
    }
}
