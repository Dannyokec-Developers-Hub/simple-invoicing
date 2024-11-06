<?php
class Invoice {
    private $dbFile = 'invoices.json';

    public function saveInvoice($data) {
        if (!file_exists($this->dbFile)) {
            file_put_contents($this->dbFile, json_encode([]));
        }

        $invoices = json_decode(file_get_contents($this->dbFile), true);
        $id = time();
        $data['id'] = $id;
        $invoices[] = $data;
        file_put_contents($this->dbFile, json_encode($invoices));

        return $data;
    }
}
?>
