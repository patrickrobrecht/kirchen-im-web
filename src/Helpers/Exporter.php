<?php

namespace KirchenImWeb\Helpers;

use RuntimeException;

/**
 * Class Exporter
 *
 * @package KirchenImWeb\Helpers
 */
class Exporter extends AbstractHelper
{
    private $dataDirectory;

    public function __construct()
    {
        parent::__construct();
        $this->dataDirectory = dirname(__FILE__, 3) . '/data';
    }

    public function export(): void
    {
        $this->checkDataDirectory();
        $entries = Database::getInstance()->getEntries();
        $this->createJSON($entries);
        $this->createCSV($entries, Configuration::getInstance()->websites);
    }

    private function checkDataDirectory(): void
    {
        if (! file_exists($this->dataDirectory) && ! mkdir($this->dataDirectory) && ! is_dir($this->dataDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $this->dataDirectory));
        }
    }

    private function createJSON(array $entries): void
    {
        $filename = $this->dataDirectory . '/data-' . date('Y-m-d') . '.json';
        $json = fopen($filename, 'wb');
        $content = json_encode(
            $this->removeNullValues($entries),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK
        );
        fwrite($json, $content);
        fclose($json);

        copy($filename, $this->dataDirectory . '/data.json');
    }

    private function createCSV(array $entries, array $websites): void
    {
        $filename = $this->dataDirectory . '/data-' . date('Y-m-d') . '.csv';

        $file = fopen($filename, 'wb');
        $headline = 'id;lat;lon;Name;Straße;PLZ;Ort;Land;Konfession;Typ';
        foreach ($websites as $websiteId => $websiteName) {
            $headline .= ';' . $websiteName;
        }
        fwrite($file, $headline . "\n");

        foreach ($entries as $row) {
            unset($row['slug']);
            $data = implode(';', $row);
            fwrite($file, $data . "\n");
        }
        fclose($file);

        copy($filename, $this->dataDirectory . '/data.csv');
    }

    public function removeNullValues($data)
    {
        foreach ($data as $id => $row) {
            if (null === $data[$id]) {
                unset($data[$id]);
            } elseif (is_array($data[$id])) {
                $data[$id] = $this->removeNullValues($data[$id]);
            }
        }
        return $data;
    }
}
