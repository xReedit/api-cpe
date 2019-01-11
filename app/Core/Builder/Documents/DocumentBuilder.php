<?php

namespace App\Core\Builder\Documents;

use App\Models\Document;

class DocumentBuilder
{
    protected $document;

    public function saveDocument($record)
    {
        $this->document = Document::create($record);

        foreach ($record['details'] as $row) {
            $this->document->details()->create($row);
        }
    }

    public function updateDocument($hash, $qr, $external_id)
    {
        $this->document->update([
            'hash' => $hash,
            'qr' => $qr,
            'external_id' => $external_id
        ]);

        return $this->document;
    }
}