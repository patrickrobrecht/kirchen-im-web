<?php

namespace KirchenImWeb\Helpers;

use RuntimeException;

class Exporter
{
    private string $dataDirectory;

    public function __construct()
    {
        $this->dataDirectory = dirname(__FILE__, 3) . '/public/data';
        if (!file_exists($this->dataDirectory) && !mkdir($this->dataDirectory) && !is_dir($this->dataDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $this->dataDirectory));
        }
    }

    private function createJSON(array $entries): void
    {
        $filename = $this->dataDirectory . '/data-temp.json';
        $json = fopen($filename, 'wb');
        $content = json_encode(
            self::removeNullValues($entries),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK
        );
        fwrite($json, $content);
        fclose($json);

        copy($filename, $this->dataDirectory . '/data.json');
    }

    private function createCSV(array $entries, array $websites): void
    {
        $filename = $this->dataDirectory . '/data-temp.csv';

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

    public static function removeNullValues($data)
    {
        foreach ($data as $id => $row) {
            if (null === $data[$id]) {
                unset($data[$id]);
            } elseif (is_array($data[$id])) {
                $data[$id] = self::removeNullValues($data[$id]);
            }
        }
        return $data;
    }

    public static function run(Database $database): void
    {
        $entries = $database->getEntries();

        $exporter = new self();
        $exporter->createJSON($entries);
        $exporter->createCSV($entries, Configuration::getWebsiteTypes());
    }
}
