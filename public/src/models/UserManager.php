<?php
require_once 'User.php';
class UserManager
{
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addUser(string $lastname, string $firstname, string $email, string $phoneNumber,
                            string $password, int $defaultNbrGuest, string $allergies)
    {
        try {
            $statement = $this->pdo->prepare('INSERT INTO users 
                    (id, lastname, firstname, email, phoneNumber, password, defaultNbrGuest, allergies) 
                    VALUES (UUID(), :lastname, :firstname,:email, :phoneNumber, :password, :defaultNbrGuest, :allergies)');
            $statement->bindValue(':lastname', $lastname);
            $statement->bindValue(':firstname', $firstname);
            $statement->bindValue(':email', $email);
            $statement->bindValue(':phoneNumber', $phoneNumber);
            $statement->bindValue(':password', password_hash($password, PASSWORD_BCRYPT));
            $statement->bindValue(':defaultNbrGuest', $defaultNbrGuest);
            $statement->bindValue(':allergies', $allergies);

            $statement->execute();
            return $regStatus = 'OK';
            } catch(PDOException $e) {
            file_put_contents('../../../db/dblogs.log', $e->getMessage().PHP_EOL, FILE_APPEND);
            return $regStatus = 'FAIL';
            }
    }

    public function connectUser(string $email, string $password)
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $statement->setFetchMode(PDO::FETCH_CLASS, 'User');
        $statement->bindValue(':email', $email);
        if ($statement->execute()) {
            if ($user = $statement->fetch()) {
                if ($user->isPasswordValid($password)) {
                    session_start();
                    $_SESSION['user'] = $user;
                    return $user;
                } else {
                    echo "<script>alert('Mot de passe erroné')</script>";
                }
            } else {
                echo "<script>alert('Utilisateur inconnu')</script>";
            }
        }
    }
}
