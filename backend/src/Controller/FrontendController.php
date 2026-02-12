<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendController extends AbstractController
{
    #[Route('/{route}', name: 'app_frontend', requirements: ['route' => '^(?!api).*'], priority: -1)]
    public function index(): Response
    {
        $indexPath = $this->getParameter('kernel.project_dir') . '/public/app/index.html';
        
        if (!file_exists($indexPath)) {
            return new Response(
                'Frontend application not found. Please build the frontend using: cd frontend && npm run build',
                Response::HTTP_NOT_FOUND
            );
        }
        
        return new Response(file_get_contents($indexPath));
    }
}
