<?php
  require_once("Bdd.php");
  Class ChildModel {
    /**
     * Gets all children associated with a parent ID, ordered by creation date
     * @param {int} $parent_id - ID of parent
     * @return {array} array of child records
     */
    public static function getChildrenByParentId($parent_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("SELECT * FROM child WHERE parent_id = :parent_id ORDER BY created_at ASC");
      $stmt->execute([
        ':parent_id' => $parent_id
      ]);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gets all children from the database, ordered by creation date
     * @return {array} array of all child records
     */
    public static function getAllChildren() {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("SELECT * FROM child ORDER BY created_at ASC");
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gets current XP of child by ID from database, returning 0 if child not found
     * @param {int} child_id - ID of child 
     * @return {int} child's XP or 0 if child not found
     */
    public static function getXpByChildId($child_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("SELECT xp FROM child WHERE id = :id");
      $stmt->execute([':id' => $child_id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      return $row ? (int) $row['xp'] : 0;
    }
    
    /**
     * Gets child details by ID from database
     * @param {int} child_id - ID of child
     * @return {array|false} associative array of child details or false if child not found
     */
    public static function getChildById($child_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("SELECT * FROM child WHERE id = :id");
      $stmt->execute([':id' => $child_id]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Logs in a child by ID and password
     * Checks child's ID and PIN against database
     * @param {int} child_id - ID of child
     * @param {string} password - PIN entered by child and to be checked
     * @return {array|false} associative array of child details or false if login fails
     */
    public static function login($child_id, $password) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("SELECT * FROM child WHERE id = :id AND password = :password");
      $stmt->execute([
        ':id' => $child_id,
        ':password' => $password
      ]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Updates child's PIN
     * @param {int} $child_id - ID of child
     * @param {string} $password - New PIN
     * @return void
     */
    public static function updatePin($child_id, $password) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE child SET password = :password WHERE id = :id");
      $stmt->execute([
        ':password' => $password,
        ':id' => $child_id
      ]);
    }

    /**
     * Creates new child profile and returns new child's ID
     * @param {string} $fullname - Full name of child
     * @param {int} $age - Age of child
     * @param {string} $avatar - Avatar icon name ofchild
     * @param {string} $pin - PIN for child
     * @param {string|null} $disease - Disease information of child, or null if none
     * @param {int} $parent_id - ID of parent
     * @return {int} ID of newly created child
     */
    public static function createChild($fullname, $age, $avatar, $pin, $disease, $parent_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("INSERT INTO child (fullname, age, avatar, password, disease, xp, streak, parent_id) VALUES (:fullname, :age, :avatar, :password, :disease, 0, 0, :parent_id) RETURNING id");
      $stmt->execute([
        ':fullname' => $fullname,
        ':age' => (int) $age,
        ':avatar' => $avatar,
        ':password' => (int) $pin,
        ':disease' => $disease ?: null,
        ':parent_id' => (int) $parent_id,
      ]);
      return (int) $stmt->fetchColumn();
    }

    /**
     * Updates child's profile information (name, age, disease)
     * @param {int} $id - ID of child
     * @param {string} $fullname - Updated full name of child
     * @param {int} $age - Updated age of child
     * @param {string|null} $disease - Updated disease information of child
     * @param {int} $parent_id - ID of parent (used to make sure they can only edit their own child)
     * @return void
     */
    public static function updateChild($id, $fullname, $age, $disease, $parent_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE child SET fullname = :fullname, age = :age, disease = :disease WHERE id = :id AND parent_id = :parent_id");
      $stmt->execute([
        ':fullname' => $fullname,
        ':age' => (int) $age,
        ':disease' => $disease ?: null,
        ':id' => (int) $id,
        ':parent_id' => (int) $parent_id,
      ]);
    }
    /**
     * Updates child profile with avatar in database
     * @param {int} $id - ID of child to update
     * @param {string} $fullname - Updated name of child
     * @param {int} $age - Updated age of child
     * @param {string} $avatar - Updated avatar of child
     * @param {string|null} $disease - Updated disease information of child
     * @param {int} $parent_id - ID of parent (used to make sure they can only edit their own child)
     * @return void
     */
    public static function updateChildWithAvatar($id, $fullname, $age, $avatar, $disease, $parent_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE child SET fullname = :fullname, age = :age, avatar = :avatar, disease = :disease WHERE id = :id AND parent_id = :parent_id");
      $stmt->execute([
        ':fullname' => $fullname,
        ':age' => (int) $age,
        ':avatar' => $avatar,
        ':disease' => $disease ?: null,
        ':id' => (int) $id,
        ':parent_id' => (int) $parent_id,
      ]);
    }
    /**
     * Deletes child profile from database
     * @param {int} $id - ID of child to delete
     * @param {int} $parent_id - ID of parent (used to make sure they can only delete their own child)
     * @return void
     */
    public static function deleteChild($id, $parent_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("DELETE FROM child WHERE id = :id AND parent_id = :parent_id");
      $stmt->execute([':id' => (int) $id, ':parent_id' => (int) $parent_id]);
    }
  }
?>
