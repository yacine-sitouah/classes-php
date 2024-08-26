<?php
class User {
    // Attributs de la classe
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;
    private $password;
    private $conn;

    // Constructeur : Initialisation de la connexion à la base de données
    public function __construct($host, $username, $password, $database) {
        $this->conn = new mysqli($host, $username, $password, $database);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Méthode pour enregistrer un nouvel utilisateur
    public function register($login, $password, $email, $firstname, $lastname) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssss", $login, $passwordHash, $email, $firstname, $lastname);

        if ($stmt->execute()) {
            // Assigner les valeurs aux attributs de l'objet
            $this->id = $this->conn->insert_id;
            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            return $this->getAllInfos();
        } else {
            return false;
        }
    }

    // Méthode pour connecter un utilisateur
    public function connect($login, $password) {
        $sql = "SELECT * FROM utilisateurs WHERE login = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Assigner les valeurs aux attributs de l'objet
                $this->id = $user['id'];
                $this->login = $user['login'];
                $this->email = $user['email'];
                $this->firstname = $user['firstname'];
                $this->lastname = $user['lastname'];
                return true;
            }
        }
        return false;
    }

    // Méthode pour déconnecter un utilisateur
    public function disconnect() {
        // Réinitialiser les attributs de l'objet
        $this->id = null;
        $this->login = null;
        $this->email = null;
        $this->firstname = null;
        $this->lastname = null;
        $this->password = null;
    }

    // Méthode pour supprimer un utilisateur
    public function delete() {
        if ($this->id) {
            $sql = "DELETE FROM utilisateurs WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $this->id);
            $stmt->execute();
            $this->disconnect();
        }
    }

    // Méthode pour mettre à jour les informations d'un utilisateur
    public function update($login, $password, $email, $firstname, $lastname) {
        if ($this->id) {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $sql = "UPDATE utilisateurs SET login = ?, password = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssssi", $login, $passwordHash, $email, $firstname, $lastname, $this->id);
            $stmt->execute();
            // Mettre à jour les attributs de l'objet
            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
        }
    }

    // Méthode pour vérifier si un utilisateur est connecté
    public function isConnected() {
        return $this->id !== null;
    }

    // Méthode pour obtenir toutes les informations de l'utilisateur
    public function getAllInfos() {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname
        ];
    }

    // Méthode pour obtenir le login
    public function getLogin() {
        return $this->login;
    }

    // Méthode pour obtenir l'email
    public function getEmail() {
        return $this->email;
    }

    // Méthode pour obtenir le prénom
    public function getFirstname() {
        return $this->firstname;
    }

    // Méthode pour obtenir le nom de famille
    public function getLastname() {
        return $this->lastname;
    }
}
?>
