<?php

include_once "abstract_ilios_model.php";

/**
 * Data Access Object (DAO) to the "recurring event" table.
 */
class Recurring_Event extends Abstract_Ilios_Model {

    public function __construct ()
    {
        parent::__construct('recurring_event', array('recurring_event_id'));

        $this->createDBHandle();
    }

    /**
     * Transactionality assumed to be handled outside this code
     *
     * @param $recurringEvent this is assumed to be the de-JSON'd version of the javascript land
     *                          model
     * @return the recurring_event_id due to the save (will be the one in the passed object if this
     *              is an update)
     */
    public function saveRecurringEvent ($recurringEvent, &$auditAtoms)
    {
        $recurringEventId = $recurringEvent['dbId'];
        $events = $recurringEvent['eventDays'];

        $DB = $this->dbHandle;

        $rowData = array();
        $rowData['on_sunday'] = $events['0'];
        $rowData['on_monday'] = $events['1'];
        $rowData['on_tuesday'] = $events['2'];
        $rowData['on_wednesday'] = $events['3'];
        $rowData['on_thursday'] = $events['4'];
        $rowData['on_friday'] = $events['5'];
        $rowData['on_saturday'] = $events['6'];

        $rowData['end_date'] = $recurringEvent['mysqldEndDate'];

        if ($recurringEvent['endDateSetExplicitly'] != 1) {
            $rowData['repetition_count'] = $recurringEvent['repetitionCount'];
        }
        else {
            $rowData['repetition_count'] = 0;
        }

        if ($recurringEventId == -1) {
            $rowData['recurring_event_id'] = null;

            $DB->insert($this->databaseTableName, $rowData);

            $recurringEventId = $DB->insert_id();

            if (! $recurringEventId) {
                return -1;
            }
            else {
                array_push($auditAtoms,
                           $this->auditEvent->wrapAtom($recurringEventId, 'recurring_event_id',
                                                       $this->databaseTableName,
                                                       Audit_Event::$CREATE_EVENT_TYPE));
            }
        }
        else {
            $DB->where('recurring_event_id', $recurringEventId);
            $DB->update($this->databaseTableName, $rowData);

            array_push($auditAtoms, $this->auditEvent->wrapAtom($recurringEventId,
                                                                'recurring_event_id',
                                                                $this->databaseTableName,
                                                                Audit_Event::$UPDATE_EVENT_TYPE));
        }

        return $recurringEventId;
    }

}