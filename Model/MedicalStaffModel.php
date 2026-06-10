<?php
require_once("Bdd.php");

class MedicalStaffModel {

  public static function getByEmail($email) {
    $db = Bdd::getInstance();
    $stmt = $db->prepare("SELECT * FROM medical_staff WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public static function getById($staff_id) {
    $db = Bdd::getInstance();
    $stmt = $db->prepare("SELECT id, fullname, email, speciality FROM medical_staff WHERE id = :id");
    $stmt->execute([':id' => (int) $staff_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public static function getPatientsByStaffId($staff_id) {
    $db   = Bdd::getInstance();
    $stmt = $db->prepare("
      SELECT c.id, c.fullname, c.age, c.disease, c.avatar, c.xp, c.streak
      FROM child c
      JOIN child_medical_staff cms ON cms.child_id = c.id
      WHERE cms.staff_id = :staff_id
      ORDER BY c.fullname ASC
    ");
    $stmt->execute([':staff_id' => (int) $staff_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function isPatientOfStaff($child_id, $staff_id) {
    $db = Bdd::getInstance();
    $stmt = $db->prepare("SELECT 1 FROM child_medical_staff WHERE child_id = :child_id AND staff_id = :staff_id LIMIT 1");
    $stmt->execute([':child_id' => (int) $child_id, ':staff_id' => (int) $staff_id]);
    return (bool) $stmt->fetchColumn();
  }

  public static function getStaffByParentId($parent_id) {
    $db   = Bdd::getInstance();
    $stmt = $db->prepare("
      SELECT ms.id, ms.fullname, ms.speciality, ms.email,
             json_agg(json_build_object('id', c.id, 'fullname', c.fullname, 'link_id', cms.id))
               FILTER (WHERE c.id IS NOT NULL) AS children
      FROM medical_staff ms
      JOIN child_medical_staff cms ON cms.staff_id = ms.id
      JOIN child c ON c.id = cms.child_id
      WHERE cms.parent_id = :parent_id
      GROUP BY ms.id
      ORDER BY ms.fullname ASC
    ");
    $stmt->execute([':parent_id' => (int) $parent_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as &$row) {
      $row['children'] = $row['children'] ? json_decode($row['children'], true) : [];
    }
    return $rows;
  }

  public static function createStaffWithPassword($fullname, $speciality, $email, $hashed_password) {
    $db = Bdd::getInstance();
    $stmt = $db->prepare("
      INSERT INTO medical_staff (fullname, speciality, email, password)
      VALUES (:fullname, :speciality, :email, :password)
      RETURNING id
    ");
    $stmt->execute([
      ':fullname' => $fullname,
      ':speciality' => $speciality,
      ':email'  => $email,
      ':password' => $hashed_password,
    ]);
    return (int) $stmt->fetchColumn();
  }

  public static function createStaff($fullname, $speciality, $email) {
    return self::createStaffWithPassword(
      $fullname, $speciality, $email,
      password_hash(bin2hex(random_bytes(8)), PASSWORD_ARGON2ID)
    );
  }

  public static function searchStaff($query) {
    $db = Bdd::getInstance();
    $stmt = $db->prepare("
      SELECT id, fullname, speciality, email
      FROM medical_staff
      WHERE fullname ILIKE :q OR email ILIKE :q
      ORDER BY fullname ASC
      LIMIT 10
    ");
    $stmt->execute([':q' => '%' . $query . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function linkToChild($staff_id, $child_id, $parent_id) {
    $db = Bdd::getInstance();
    $stmt = $db->prepare("
      INSERT INTO child_medical_staff (staff_id, child_id, parent_id)
      VALUES (:staff_id, :child_id, :parent_id)
    ");
    $stmt->execute([
      ':staff_id' => (int) $staff_id,
      ':child_id' => (int) $child_id,
      ':parent_id' => (int) $parent_id,
    ]);
  }

  public static function unlinkFromChild($link_id, $parent_id) {
    $db = Bdd::getInstance();
    $stmt = $db->prepare("DELETE FROM child_medical_staff WHERE id = :id AND parent_id = :parent_id");
    $stmt->execute([':id' => (int) $link_id, ':parent_id' => (int) $parent_id]);
  }

  public static function updateStaff($staff_id, $fullname, $speciality, $email, $parent_id) {
    $db = Bdd::getInstance();
    $stmt = $db->prepare("
      UPDATE medical_staff SET fullname = :fullname, speciality = :speciality, email = :email
      WHERE id = :id
        AND EXISTS (SELECT 1 FROM child_medical_staff WHERE staff_id = :id2 AND parent_id = :parent_id)
    ");
    $stmt->execute([
      ':fullname' => $fullname,
      ':speciality' => $speciality,
      ':email' => $email,
      ':id' => $staff_id,
      ':id2' => $staff_id,
      ':parent_id' => $parent_id,
    ]);
  }

  public static function deleteStaff($staff_id, $parent_id) {
    $db = Bdd::getInstance();
    $stmt = $db->prepare("DELETE FROM child_medical_staff WHERE staff_id = :staff_id AND parent_id = :parent_id");
    $stmt->execute([':staff_id' => $staff_id, ':parent_id' => $parent_id]);
  }
}
?>