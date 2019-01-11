<?php

namespace App\Core\Builder\Documents;

use App\Models\Annulment;

class AnnulmentBuilder
{
    protected $annulment;

    public function save($inputs)
    {
        $this->annulment = new Annulment();
        $this->annulment->fill($inputs);
        $this->annulment->soap_type_id = '01';
        $this->annulment->state_type_id = '01';
        $this->annulment->filename = '';
        $this->annulment->save();

        $documents = $inputs['documents'];
        foreach ($documents as $document)
        {
            $this->annulment->documents()->create([
                'document_id' => $document['id'],
                'description' => $document['description']
            ]);
        }
    }

    public function getAnnulment()
    {
        return Annulment::with(['documents'])->find($this->annulment->id);
    }
}