<?php
// app/controllers/HomeController.php
require_once '../models/Stats.php';

class HomeController {
    public function index() {
        // Créer une instance du modèle Stats pour récupérer les données
        $statsModel = new Stats();
        $total_users = $statsModel->getTotalUsers();
        $total_accounts = $statsModel->getTotalAccounts();
        $total_bets = $statsModel->getTotalBets();

        // Passer ces données à la vue d'accueil
        require_once '../views/home.php';
    }
}
?>
