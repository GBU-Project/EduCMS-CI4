<?php

/**
 * CI3-compatible DB Utility (dbutil). CI4 dropped the DB Utility
 * library entirely, so a minimal full-database SQL export is provided.
 */
#[AllowDynamicProperties]
class CI_DB_utility
{
    /** @var \CodeIgniter\Database\BaseConnection */
    protected $connection;

    public function __construct()
    {
        $instance = Compat::instance();
        $this->connection = $instance->db->connection();
    }

    public function backup($params = [])
    {
        $format = $params['format'] ?? 'sql';

        $out = "-- EduCMS Database Backup\n";
        $out .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $out .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        try {
            $tables = $this->connection->listTables();
        } catch (\Throwable $e) {
            return false;
        }

        foreach ($tables as $table) {
            $row = $this->connection->query("SHOW CREATE TABLE `{$table}`")->getRowArray();
            if ($row) {
                $create = array_values($row)[1] ?? '';
                $out .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $out .= $create . ";\n\n";
            }

            $rows = $this->connection->query("SELECT * FROM `{$table}`")->getResultArray();
            foreach ($rows as $r) {
                $cols = array_keys($r);
                $values = [];
                foreach ($r as $v) {
                    $values[] = $v === null ? 'NULL' : $this->connection->escape($v);
                }
                $out .= "INSERT INTO `{$table}` (`" . implode('`, `', $cols) . "`) VALUES (" . implode(', ', $values) . ");\n";
            }
            $out .= "\n";
        }

        $out .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        if ($format === 'gzip') {
            $out = gzencode($out);
        }

        return $out;
    }
}
