<?php
try {
    $db = new PDO(
        "pgsql:host=aws-0-eu-west-1.pooler.supabase.com;port=6543;dbname=postgres",
        "postgres.mrevazltwkhjmjyhxboa",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
echo "Connexion réussie !";
?>


<?php
class Bdd {
    private static $instance = null;

    /**
     * Returns single shared database connection instance, creating it if it doesn't exist yet
     * @return {PDO} activate database connection
     */
    public static function getInstance() {
        if (self::$instance === null) {
            try {
                $db = new PDO(
                    "pgsql:host=aws-0-eu-west-1.pooler.supabase.com;port=6543;dbname=postgres",
                    "postgres.mrevazltwkhjmjyhxboa",
                    "",
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            }  catch (Exception $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
