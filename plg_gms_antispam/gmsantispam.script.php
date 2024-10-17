<?php
defined('_JEXEC') or die;

use Joomla\CMS\Installer\InstallerScript;
use Joomla\Database\DatabaseDriver;

class PlgCaptchaHoneypotInstallerScript extends InstallerScript
{
    /**
     * Methode zur Ausführung von Aktionen vor der Installation des Plugins
     *
     * @param   string                    $type    Der Installationstyp (z.B. install, update oder uninstall)
     * @param   Joomla\CMS\Installer\Adapter  $parent  Der Klassen-Aufruf-Objekt
     *
     * @return  boolean  True, wenn die Aktion erfolgreich war
     */
    public function preflight($type, $parent)
    {
        // Hier können Sie Aktionen vor der Installation ausführen
        return true;
    }

    /**
     * Methode zur Ausführung von Aktionen nach der Installation des Plugins
     *
     * @param   Joomla\CMS\Installer\Adapter  $parent  Der Klassen-Aufruf-Objekt
     *
     * @return  boolean  True, wenn die Aktion erfolgreich war
     */
    public function install($parent)
    {
        $db = $this->db;

        // Erstellen Sie die Tabelle für Honeypot-Logs
        $query = "CREATE TABLE IF NOT EXISTS `#__honeypot_logs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `ip` varchar(45) NOT NULL,
        `reason` varchar(45) NOT NULL,
        `form_id` varchar(45) NOT NULL,
        `date` datetime NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;";

        $db->setQuery($query);
        $db->execute();

        // Erstellen Sie die Tabelle für blockierte IPs
        $query = "CREATE TABLE IF NOT EXISTS `#__honeypot_blocked_ips` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `ip` varchar(45) NOT NULL,
        `block_expires` datetime NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;";

        $db->setQuery($query);
        $db->execute();

        return true;
    }

    /**
     * Methode zur Ausführung von Aktionen bei der Aktualisierung des Plugins
     *
     * @param   Joomla\CMS\Installer\Adapter  $parent  Der Klassen-Aufruf-Objekt
     *
     * @return  boolean  True, wenn die Aktion erfolgreich war
     */
    public function update($parent)
    {
        // Hier können Sie Aktionen bei der Aktualisierung ausführen
        return true;
    }
}