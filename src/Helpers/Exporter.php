<?php
namespace KirchenImWeb\Helpers;

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
        $this->dataDirectory = dirname(__FILE__, 3) . '/data';
    }

    public function export()
    {
        $this->checkDataDirectory();
        $entries = Database::getInstance()->getEntries();
        $this->createJSON($entries);
        $this->createCSV($entries, Configuration::getInstance()->websites);
    }

    private function checkDataDirectory()
    {
        if (!file_exists($this->dataDirectory) || !is_dir($this->dataDirectory)) {
            mkdir($this->dataDirectory);
        }
    }

    private function createJSON(array $entries)
    {
        $filename = $this->dataDirectory . '/data-' . date('Y-m-d') . '.json';
        $json = fopen($filename, 'w');
        $content = json_encode(
            $this->removeNullValues($entries),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK
        );
        fwrite($json, $content);
        fclose($json);

        copy($filename, $this->dataDirectory . '/data.json');
    }

    private function createCSV(array $entries, array $websites)
    {
        $filename = $this->dataDirectory . '/data-' . date('Y-m-d') . '.csv';

        $file = fopen($filename, 'w');
        $headline = 'id;lat;lon;Name;Straße;PLZ;Ort;Land;Konfession;Typ';
        foreach ($websites as $websiteId => $websiteName) {
            $headline .= ';' . $websiteName;
        }
        fwrite($file, $headline . "\n");

        foreach ($entries as $row) {
            unset($row['slug']);
            $data = implode(";", $row);
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
