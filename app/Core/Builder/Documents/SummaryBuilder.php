<?php

namespace App\Core\Builder\Documents;

use App\Models\Summary;

class SummaryBuilder
{
    protected $summary;

    public function save($inputs)
    {
        $this->summary = new Summary();
        $this->summary->fill($inputs);
        $this->summary->soap_type_id = '01';
        $this->summary->state_type_id = '01';
        $this->summary->filename = '';
        $this->summary->save();

        $documents = $inputs['documents'];
        foreach ($documents as $document)
        {
            $this->summary->documents()->create([
                'document_id' => $document['id']
            ]);
        }
    }

    public function getSummary()
    {
        return Summary::with(['documents'])->find($this->summary->id);
    }
}