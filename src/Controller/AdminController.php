<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    public function panel()
    {
        return $this->render('admin/panel.html.twig', [
            //'number' => $number,
        ]);
    }
}