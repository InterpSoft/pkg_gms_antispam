<?php
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;

class PlgCaptchaHoneypot extends CMSPlugin
{
    protected $app;
    protected $autoloadLanguage = true;

    public function onInit($id = 'dynamic_recaptcha_1')
    {
        $startTime = microtime(true);

        $document = Factory::getDocument();
        $session = Factory::getSession();

        $honeypotField = $this->createCustomHoneypotField($id);
        $session->set('honeypot_field_' . $id, $honeypotField);

        $js = <<<JS
            document.addEventListener('DOMContentLoaded', function() {
                var forms = document.querySelectorAll('form');
                forms.forEach(function(form) {
                    var honeypot = document.createElement('input');
                    honeypot.type = '{$honeypotField['type']}';
                    honeypot.name = '{$honeypotField['name']}';
                    honeypot.setAttribute('aria-hidden', 'true');
                    honeypot.style.cssText = '{$honeypotField['style']}';
                    form.appendChild(honeypot);

                    var timestamp = document.createElement('input');
                    timestamp.type = 'hidden';
                    timestamp.name = 'form_timestamp';
                    timestamp.value = Date.now();
                    form.appendChild(timestamp);

                    var formId = document.createElement('input');
                    formId.type = 'hidden';
                    formId.name = 'honeypot_form_id';
                    formId.value = '$id';
                    form.appendChild(formId);
                });
            });
            JS;

        $document->addScriptDeclaration($js);

        $this->logPerformance($startTime);
        return true;
    }

    public function onCheckAnswer($code = null)
    {
        $startTime = microtime(true);

        $input = $this->app->input;
        $session = Factory::getSession();

        #$honeypotName = $session->get('honeypot_name');
        $formId = $input->get('honeypot_form_id', '', 'string');
        $honeypotField = $session->get('honeypot_field_' . $formId);

        if (!$honeypotField) {
            // Fehler: Honeypot-Feld nicht gefunden
            $this->logAttempt($input->server->getString('REMOTE_ADDR'), 'invalid_honeypot', $formId);
            $this->app->enqueueMessage(Text::_('PLG_CAPTCHA_HONEYPOT_INVALID_FORM'), 'error');
            return false;
        }

        $honeypot = $input->get($honeypotField['name'], '', 'string');
        $timestamp = $input->get('form_timestamp', 0, 'int');

        $formSettings = $this->getFormSettings($formId);
        $minTime = $formSettings['min_time'] * 1000;

        $ip = $input->server->getString('REMOTE_ADDR');

        // Überprüfen Sie, ob wir auf reCAPTCHA umgeschaltet haben
        /*if ($session->get('honeypot_use_recaptcha', false)) {
            // Verwenden Sie reCAPTCHA zur Überprüfung
            JPluginHelper::importPlugin('captcha', 'recaptcha');
            $dispatcher = JEventDispatcher::getInstance();
            $results = $dispatcher->trigger('onCheckAnswer', $code);
            return !empty($results[0]);
        }*/

        // Überprüfe, ob die IP blockiert ist
        if ($this->checkAndBlockIP($ip)) {
            $this->app->enqueueMessage(Text::_('PLG_CAPTCHA_HONEYPOT_IP_BLOCKED'), 'error');
            return false;
        }

        // Überprüfe, ob das Honeypot-Feld ausgefüllt wurde
        if (!empty($honeypot)) {
            $this->logAttempt($ip, 'honeypot_filled', $formId);
            $this->app->enqueueMessage(Text::_('PLG_CAPTCHA_HONEYPOT_SPAM_DETECTED'), 'error');
            return false;
        }

        // Überprüfe, ob das Formular zu schnell ausgefüllt wurde
        if ((time() * 1000 - $timestamp) < $minTime) {
            $this->logAttempt($ip, 'too_fast', $formId);
            $this->app->enqueueMessage(Text::_('PLG_CAPTCHA_HONEYPOT_FORM_SUBMITTED_TOO_QUICKLY'), 'error');
            return false;
        }

        // Zusätzliche Bot-Erkennung
        if ($this->isSuspiciousRequest()) {
            $this->logAttempt($ip, 'suspicious_request', $formId);
            $this->app->enqueueMessage(Text::_('PLG_CAPTCHA_HONEYPOT_SUSPICIOUS_REQUEST'), 'error');
            return false;
        }

        // Wenn die Überprüfung fehlschlägt, rufen Sie switchToReCaptcha() auf
        /*if ($failed) {
            $this->switchToReCaptcha();
            return false;
        }*/

        $this->logPerformance($startTime);
        return true;
    }

    protected function logAttempt($ip, $reason, $formId)
    {
        if (!$this->params->get('enable_logging', 1)) {
            return;
        }

        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->insert($db->quoteName('#__honeypot_logs'))
            ->columns($db->quoteName(['ip', 'reason', 'form_id', 'date']))
            ->values(
                $db->quote($ip) . ', ' .
                $db->quote($reason) . ', ' .
                $db->quote($formId) . ', ' .
                $db->quote(Factory::getDate()->toSql())
            );

        $db->setQuery($query);
        $db->execute();

        $this->rotateLogTable();
    }

    protected function rotateLogTable()
    {
        $db = Factory::getDbo();
        $maxRows = $this->params->get('log_max_rows', 10000);

        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__honeypot_logs'));

        $db->setQuery($query);
        $count = $db->loadResult();

        if ($count > $maxRows) {
            $query = $db->getQuery(true)
                ->delete($db->quoteName('#__honeypot_logs'))
                ->order($db->quoteName('date') . ' ASC')
                ->setLimit($count - $maxRows);

            $db->setQuery($query);
            $db->execute();
        }
    }

    protected function isSuspiciousRequest()
    {
        $input = $this->app->input;
        $userAgent = $input->server->getString('HTTP_USER_AGENT', '');
        $acceptLanguage = $input->server->getString('HTTP_ACCEPT_LANGUAGE', '');

        // Überprüfe auf leeren User-Agent oder fehlende Accept-Language
        if (empty($userAgent) || empty($acceptLanguage)) {
            return true;
        }

        // Hier können weitere Überprüfungen hinzugefügt werden

        return false;
    }

    protected function checkAndBlockIP($ip)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__honeypot_blocked_ips'))
            ->where($db->quoteName('ip') . ' = ' . $db->quote($ip))
            ->where($db->quoteName('block_expires') . ' > ' . $db->quote(Factory::getDate()->toSql()));

        $db->setQuery($query);
        $isBlocked = (bool) $db->loadResult();

        if ($isBlocked) {
            return true;
        }

        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__honeypot_attempts'))
            ->where($db->quoteName('ip') . ' = ' . $db->quote($ip))
            ->where($db->quoteName('attempt_time') . ' > ' . $db->quote(Factory::getDate()->modify('-1 hour')->toSql()));

        $db->setQuery($query);
        $attempts = (int) $db->loadResult();

        $maxAttempts = $this->params->get('max_attempts', 5);

        if ($attempts >= $maxAttempts) {
            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__honeypot_blocked_ips'))
                ->columns([$db->quoteName('ip'), $db->quoteName('block_expires')])
                ->values($db->quote($ip) . ', ' . $db->quote(Factory::getDate()->modify('+1 hour')->toSql()));

            $db->setQuery($query);
            $db->execute();

            return true;
        }

        return false;
    }

    public function getStatistics()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*) as total_attempts')
            ->select('SUM(CASE WHEN reason = "honeypot_filled" THEN 1 ELSE 0 END) as honeypot_filled')
            ->select('SUM(CASE WHEN reason = "too_fast" THEN 1 ELSE 0 END) as too_fast')
            ->select('SUM(CASE WHEN reason = "suspicious_request" THEN 1 ELSE 0 END) as suspicious_request')
            ->select('COUNT(DISTINCT ip) as unique_ips')
            ->from($db->quoteName('#__honeypot_logs'))
            ->where($db->quoteName('date') . ' > ' . $db->quote(Factory::getDate()->modify('-30 days')->toSql()));

        $db->setQuery($query);
        return $db->loadObject();
    }

    public function onInstallerBeforePackageDownload(&$url, &$headers)
    {
        $uri = JUri::getInstance($url);

        if ($uri->getHost() == 'your-update-server.com' && strpos($uri->getPath(), 'plg_captcha_honeypot') !== false) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName('manifest_cache'))
                ->from($db->quoteName('#__extensions'))
                ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
                ->where($db->quoteName('folder') . ' = ' . $db->quote('captcha'))
                ->where($db->quoteName('element') . ' = ' . $db->quote('honeypot'));

            $db->setQuery($query);
            $manifest = json_decode($db->loadResult(), true);

            $current_version = $manifest['version'];

            $headers['X-Current-Version'] = $current_version;
        }

        return true;
    }

    protected function getFormSettings($formId)
    {
        $defaultSettings = [
            'min_time' => $this->params->get('min_time', 3),
            'enable_logging' => $this->params->get('enable_logging', 1),
            'log_max_size' => $this->params->get('log_max_size', 5),
        ];

        $formSpecificSettings = $this->params->get('form_specific_settings', []);

        if (isset($formSpecificSettings[$formId])) {
            return array_merge($defaultSettings, $formSpecificSettings[$formId]);
        }

        return $defaultSettings;
    }

    protected function switchToReCaptcha()
    {
        $session = Factory::getSession();
        $session->set('honeypot_use_recaptcha', true);

        // Laden Sie das reCAPTCHA-Plugin
        JPluginHelper::importPlugin('captcha', 'recaptcha');
        $dispatcher = JEventDispatcher::getInstance();

        // Initialisieren Sie reCAPTCHA
        $dispatcher->trigger('onInit', ['dynamic_recaptcha_1']);
    }

    public function generateReport($startDate, $endDate)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('DATE(date) as day, COUNT(*) as total, reason')
            ->from($db->quoteName('#__honeypot_logs'))
            ->where($db->quoteName('date') . ' BETWEEN ' . $db->quote($startDate) . ' AND ' . $db->quote($endDate))
            ->group('DATE(date), reason')
            ->order('DATE(date) ASC');

        $db->setQuery($query);
        $results = $db->loadObjectList();

        $report = [];
        foreach ($results as $row) {
            if (!isset($report[$row->day])) {
                $report[$row->day] = ['total' => 0, 'honeypot_filled' => 0, 'too_fast' => 0, 'suspicious_request' => 0];
            }
            $report[$row->day]['total'] += $row->total;
            $report[$row->day][$row->reason] = $row->total;
        }

        return $report;
    }

    public function getTopSpamIPs($limit = 10)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('ip, COUNT(*) as total')
            ->from($db->quoteName('#__honeypot_logs'))
            ->group('ip')
            ->order('total DESC')
            ->setLimit($limit);

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function checkPluginHealth()
    {
        $health = ['status' => 'good', 'issues' => []];

        // Überprüfen Sie, ob die Logging-Tabelle existiert
        $db = Factory::getDbo();
        $tableExists = $db->setQuery("SHOW TABLES LIKE '#__honeypot_logs'")->loadResult();
        if (!$tableExists) {
            $health['status'] = 'warning';
            $health['issues'][] = 'Logging table does not exist';
        }

        // Überprüfen Sie die Anzahl der Logs
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__honeypot_logs'));
        $db->setQuery($query);
        $logCount = $db->loadResult();

        $maxLogs = $this->params->get('log_max_rows', 10000);
        if ($logCount > $maxLogs * 0.9) {
            $health['status'] = 'warning';
            $health['issues'][] = 'Log table is nearing capacity';
        }

        // Überprüfen Sie die Plugin-Einstellungen
        if ($this->params->get('min_time', 3) < 2) {
            $health['status'] = 'warning';
            $health['issues'][] = 'Minimum time setting may be too low';
        }

        if (!$this->params->get('enable_logging', 1)) {
            $health['status'] = 'warning';
            $health['issues'][] = 'Logging is disabled';
        }

        return $health;
    }

    public function cleanupOldData($days = 30)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__honeypot_logs'))
            ->where($db->quoteName('date') . ' < ' . $db->quote(Factory::getDate()->modify('-' . $days . ' days')->toSql()));

        $db->setQuery($query);
        $db->execute();

        return $db->getAffectedRows();
    }

    public function exportConfig()
    {
        $config = [
            'params' => $this->params->toArray(),
            'version' => $this->_name . ' ' . $this->_type . ' v' . $this->_version
        ];

        return json_encode($config, JSON_PRETTY_PRINT);
    }

    public function importConfig($configJson)
    {
        $config = json_decode($configJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON configuration');
        }

        // Überprüfen Sie die Version
        if (!isset($config['version']) || strpos($config['version'], $this->_name) !== 0) {
            throw new Exception('Invalid configuration version');
        }

        // Aktualisieren Sie die Parameter
        if (isset($config['params']) && is_array($config['params'])) {
            foreach ($config['params'] as $key => $value) {
                $this->params->set($key, $value);
            }

            // Speichern Sie die aktualisierten Parameter
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__extensions'))
                ->set($db->quoteName('params') . ' = ' . $db->quote($this->params->toString()))
                ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
                ->where($db->quoteName('folder') . ' = ' . $db->quote('captcha'))
                ->where($db->quoteName('element') . ' = ' . $db->quote('honeypot'));

            $db->setQuery($query);
            $db->execute();

            return true;
        }

        throw new Exception('Invalid configuration structure');
    }

    public function createBackup()
    {
        $db = Factory::getDbo();
        $config = JFactory::getConfig();
        $backupDir = $config->get('tmp_path') . '/honeypot_backups';

        if (!JFolder::exists($backupDir)) {
            JFolder::create($backupDir);
        }

        $backupFile = $backupDir . '/honeypot_backup_' . date('Y-m-d_H-i-s') . '.sql';

        $tables = ['#__honeypot_logs'];
        $return = '';

        foreach ($tables as $table) {
            $result = $db->getTableCreate($table);
            $return .= "DROP TABLE IF EXISTS `" . $table . "`;\n\n";
            $return .= $result[$table];
            $return .= ";\n\n";

            $rows = $db->setQuery('SELECT * FROM ' . $table)->loadObjectList();
            foreach ($rows as $row) {
                $fields = [];
                foreach (get_object_vars($row) as $key => $value) {
                    $fields[] = $db->quote($value);
                }
                $return .= 'INSERT INTO ' . $table . ' VALUES (' . implode(',', $fields) . ");\n";
            }
            $return .= "\n";
        }

        if (JFile::write($backupFile, $return)) {
            return $backupFile;
        }

        return false;
    }

    public function restoreBackup($backupFile)
    {
        if (!JFile::exists($backupFile)) {
            throw new Exception('Backup file does not exist');
        }

        $db = Factory::getDbo();
        $sql = JFile::read($backupFile);

        $queries = $db->splitSql($sql);

        foreach ($queries as $query) {
            $query = trim($query);
            if ($query) {
                $db->setQuery($query);
                try {
                    $db->execute();
                } catch (Exception $e) {
                    throw new Exception('Error executing SQL: ' . $e->getMessage());
                }
            }
        }

        return true;
    }

    protected function logPerformance($startTime)
    {
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->insert($db->quoteName('#__honeypot_performance'))
            ->columns($db->quoteName(['date', 'execution_time']))
            ->values(
                $db->quote(Factory::getDate()->toSql()) . ', ' .
                $db->quote($executionTime)
            );

        $db->setQuery($query);
        $db->execute();
    }

    public function analyzePerformance($days = 7)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('AVG(execution_time) as avg_time, MAX(execution_time) as max_time, MIN(execution_time) as min_time')
            ->from($db->quoteName('#__honeypot_performance'))
            ->where($db->quoteName('date') . ' >= ' . $db->quote(Factory::getDate()->modify('-' . $days . ' days')->toSql()));

        $db->setQuery($query);
        return $db->loadObject();
    }

    public function detectSuspiciousPatterns()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('ip, COUNT(*) as attempt_count')
            ->from($db->quoteName('#__honeypot_logs'))
            ->where($db->quoteName('date') . ' >= ' . $db->quote(Factory::getDate()->modify('-1 hour')->toSql()))
            ->group($db->quoteName('ip'))
            ->having('attempt_count > 10');

        $db->setQuery($query);
        $suspiciousIPs = $db->loadObjectList();

        if (!empty($suspiciousIPs)) {
            // Benachrichtigen Sie den Administrator oder ergreifen Sie andere Maßnahmen
            $this->notifyAdmin('Suspicious activity detected', $suspiciousIPs);
        }
    }

    protected function notifyAdmin($subject, $data)
    {
        $config = Factory::getConfig();
        $mailer = Factory::getMailer();

        $mailer->setSender([$config->get('mailfrom'), $config->get('fromname')]);
        $mailer->addRecipient($config->get('mailfrom'));
        $mailer->setSubject($subject);

        $body = "The following suspicious activity has been detected:\n\n";
        foreach ($data as $item) {
            $body .= "IP: {$item->ip}, Attempts: {$item->attempt_count}\n";
        }

        $mailer->setBody($body);
        $mailer->Send();
    }

    public function validateSettings($settings)
    {
        $errors = [];

        if (!isset($settings['min_time']) || $settings['min_time'] < 1) {
            $errors[] = 'Minimum time must be at least 1 second';
        }

        if (!isset($settings['log_max_rows']) || $settings['log_max_rows'] < 1000) {
            $errors[] = 'Maximum log rows must be at least 1000';
        }

        if (!isset($settings['enable_logging'])) {
            $errors[] = 'Enable logging setting is missing';
        }

        // Weitere Validierungen hier...

        return $errors;
    }

    protected function createCustomHoneypotField($formId)
    {
        $fieldTypes = ['text', 'email', 'tel', 'url'];
        $fieldType = $fieldTypes[array_rand($fieldTypes)];

        $commonNames = ['name', 'email', 'phone', 'address', 'comment', 'message', 'subject'];
        $fieldName = $commonNames[array_rand($commonNames)] . '_' . bin2hex(random_bytes(4));

        $styles = [
            'position:absolute!important;left:-9999px!important;',
            'display:none!important;',
            'opacity:0!important;height:0!important;width:0!important;',
        ];
        $style = $styles[array_rand($styles)];

        return [
            'type' => $fieldType,
            'name' => $fieldName,
            'style' => $style
        ];
    }


}