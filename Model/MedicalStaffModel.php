<?php
require_once("Bdd.php");

class MedicalStaffModel {

  /**
   * Gets all medical staff associated with a parent ID, including the children they are linked to
   * @param {int} $parent_id - ID of parent
   * @return {array} array of medical staff records, each with an additional 'children
   */
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

  /**
   * Creates a new medical staff member with randomly generated password
   * @param {string} $fullname - Full name of medical staff
   * @param {string} $speciality - Speciality of medical staff
   * @param {string} $email - Email of medical staff
   * @return {int} ID of newly created medical staff
   */
  public static function createStaff($fullname, $speciality, $email) {
    $db   = Bdd::getInstance();
    $stmt = $db->prepare("
      INSERT INTO medical_staff (fullname, speciality, email, password)
      VALUES (:fullname, :speciality, :email, :password)
      RETURNING id
    ");
    $stmt->execute([
      ':fullname'   => $fullname,
      ':speciality' => $speciality,
      ':email'      => $email,
      ':password'   => password_hash(bin2hex(random_bytes(8)), PASSWORD_ARGON2ID),
    ]);
    return (int) $stmt->fetchColumn();
  }

  /**
   * Links medical staff member to child under specific parent
   * @param {int} $staff_id - ID of medical staff
   * @param {int} $child_id - ID of child
   * @param {int} $parent_id - ID of parent
   * @return void
   */
  public static function linkToChild($staff_id, $child_id, $parent_id) {
    $db   = Bdd::getInstance();
    $stmt = $db->prepare("
      INSERT INTO child_medical_staff (staff_id, child_id, parent_id)
      VALUES (:staff_id, :child_id, :parent_id)
    ");
    $stmt->execute([
      ':staff_id'  => (int) $staff_id,
      ':child_id'  => (int) $child_id,
      ':parent_id' => (int) $parent_id,
    ]);
  }

  /**
   * Unlinks medical staff member from child
   * @param {int} $link_id - ID of link between medical staff and child
   * @param {int} $parent_id - ID of parent (makes sure they can only remove their own links)
   * @return void
   */
  public static function unlinkFromChild($link_id, $parent_id) {
    $db   = Bdd::getInstance();
    $stmt = $db->prepare("DELETE FROM child_medical_staff WHERE id = :id AND parent_id = :parent_id");
    $stmt->execute([':id' => (int) $link_id, ':parent_id' => (int) $parent_id]);
  }

  /**
   * Updates medical staff member's profile details - only allowed if parent is linked to them
   * @param {int} $staff_id - ID of medical staff
   * @param {string} $fullname - Updated full name of medical staff
   * @param {string} $speciality - Updated speciality of medical staff
   * @param {string} $email - Updated email of medical staff
   * @param {int} $parent_id - ID of parent (used to make sure they can only edit their own staff)
   * @return void
   */
  public static function updateStaff($staff_id, $fullname, $speciality, $email, $parent_id) {
    $db   = Bdd::getInstance();
    $stmt = $db->prepare("
      UPDATE medical_staff SET fullname = :fullname, speciality = :speciality, email = :email
      WHERE id = :id
        AND EXISTS (SELECT 1 FROM child_medical_staff WHERE staff_id = :id2 AND parent_id = :parent_id)
    ");
    $stmt->execute([
      ':fullname'   => $fullname,
      ':speciality' => $speciality,
      ':email'      => $email,
      ':id'         => $staff_id,
      ':id2'        => $staff_id,
      ':parent_id'  => $parent_id,
    ]);
  }

  /**
   * Deletes medical staff member from database, along with all their links to children under this parent
   * @param {int} $staff_id - ID of medical staff to delete
   * @param {int} $parent_id - ID of parent (used to restrict deletion to their own staff)
   * @return void
   */
  public static function deleteStaff($staff_id, $parent_id) {
    $db   = Bdd::getInstance();
    $stmt = $db->prepare("DELETE FROM child_medical_staff WHERE staff_id = :staff_id AND parent_id = :parent_id");
    $stmt->execute([':staff_id' => $staff_id, ':parent_id' => $parent_id]);
    $stmt = $db->prepare("DELETE FROM medical_staff WHERE id = :id");
    $stmt->execute([':id' => $staff_id]);
  }
}
?>
