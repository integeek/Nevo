<?php
class Bdd
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    "pgsql:host=aws-0-eu-west-1.pooler.supabase.com;port=6543;dbname=postgres",
                    "postgres.mrevazltwkhjmjyhxboa",
                    getenv("DB_PASSWORD") ?: "",
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (Exception $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
