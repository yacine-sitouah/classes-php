<?php
class User {
    // Attributs de la classe
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    // Connexion à la base de données
    private $conn;

    public function __construct($host, $username, $password, $database) {
        $this->conn = new mysqli($host, $username, $password, $database);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Méthode pour créer un nouvel utilisateur
    public function create($login, $password, $email, $firstname, $lastname) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssss", $login, $passwordHash, $email, $firstname, $lastname);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }

    // Méthode pour lire un utilisateur par ID
    public function read($id) {
        $sql = "SELECT * FROM utilisateurs WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    // Méthode pour mettre à jour un utilisateur
    public function update($id, $login, $email, $firstname, $lastname) {
        $sql = "UPDATE utilisateurs SET login = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $login, $email, $firstname, $lastname, $id);

        return $stmt->execute();
    }

    // Méthode pour supprimer un utilisateur
    public function delete($id) {
        $sql = "DELETE FROM utilisateurs WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    // Méthode pour fermer la connexion
    public function closeConnection() {
        $this->conn->close();
    }
}
