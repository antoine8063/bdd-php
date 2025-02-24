<?php
class Database {
    // Variable pour stocker l'instance unique de la classe
    private static $instance = null;

    // Variables pour les informations de connexion à la base de données
    private $conn;
    private $host = 'localhost'; // Nom d'hôte (localhost si tu es en local)
    private $username = 'root'; // Nom d'utilisateur MySQL
    private $password = ''; // Mot de passe MySQL (vide par défaut en local)
    private $dbname = 'betfactory'; // Nom de ta base de données

    // Constructeur privé pour éviter la création d'une nouvelle instance directement
    private function __construct() {
        try {
            // Connexion à la base de données via PDO
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
            
            // Définir le mode d'erreur de PDO pour qu'il lance des exceptions en cas d'erreurs
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Afficher un message de succès si la connexion est réussie (optionnel)
            // echo "Connexion réussie!";
        } catch (PDOException $e) {
            // En cas d'échec, on attrape l'exception et on affiche un message d'erreur
            echo "Erreur de connexion à la base de données: " . $e->getMessage();
        }
    }

    // Méthode pour obtenir l'instance unique de la classe (Pattern Singleton)
    public static function getInstance() {
        // Si l'instance n'est pas déjà créée, on la crée
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Méthode pour récupérer l'objet PDO de connexion à la base de données
    public function getConnection() {
        return $this->conn;
    }

    // Méthode pour fermer la connexion à la base de données
    public function closeConnection() {
        $this->conn = null;
    }
}
?>
