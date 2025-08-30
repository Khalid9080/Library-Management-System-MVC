<?php
// MVC/Model/User.php
class User
{
  private PDO $db;
  public function __construct(PDO $db) { $this->db = $db; }

  public function findByEmail(string $email): ?array {
    $stmt = $this->db->prepare("SELECT u.*, r.name AS role_name FROM users u
                                JOIN roles r ON r.id = u.role_id
                                WHERE u.email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    return $row ?: null;
  }

  public function emailExists(string $email): bool {
    $stmt = $this->db->prepare("SELECT 1 FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return (bool)$stmt->fetchColumn();
  }

  public function create(array $data): int {
    $stmt = $this->db->prepare("
      INSERT INTO users (username, email, phone, password_hash, role_id)
      VALUES (:username, :email, :phone, :password_hash, :role_id)
    ");
    $stmt->execute([
      ':username' => $data['username'],
      ':email' => $data['email'],
      ':phone' => $data['phone'] ?? null,
      ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
      ':role_id' => $data['role_id'],
    ]);
    return (int)$this->db->lastInsertId();
  }
}
