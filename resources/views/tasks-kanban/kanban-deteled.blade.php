<?php

 // The function handling the event
 public function recordDeleted(array $record): void
    {
        // The $record object contains the details of the record to delete
        if (isset($record['id']) && $index = $this->recordIndexById($record['id'])) {
            unset($this->records[$index]);
        }
    }
