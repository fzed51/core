<?php

namespace fzed51\Core;

use Exception;
use PDO;

/**
 * Description of PDOFactory
 *
 * @author fabien.sanchez
 */
class PDOFactory {

    static private function configPdo(PDO $pdo) {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        return $pdo;
    }

    static public function sqlite($filename) {
        if (file_exists($filename)) {
            $filename = realpath($filename);
            $pdo = new PDO('sqlite:' . $filename);
        } else {
            throw new Exception("Le fichier $filename n'existe pas !");
        }
        return self::configPdo($pdo);
    }

    static public function oci($sid, $user, $password) {
        $pdo = new PDO("oci:dbname=$sid", $user, $password);
        return self::configPdo($pdo);
    }

}
