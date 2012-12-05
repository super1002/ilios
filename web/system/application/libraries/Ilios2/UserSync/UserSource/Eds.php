<?php

/**
 * Client to UCSF's campus directory - the Enterprise Data Services (EDS).
 * Implements the external user source interface.
 * Used for synchronizing the Ilios-internal user store against the EDS.
 */
class Ilios2_UserSync_UserSource_Eds implements Ilios2_UserSync_UserSource
{

    /**
     * base DN for searches in EDS
     * @var string
     */
    const EDS_BASE_DN = 'ou=people,dc=ucsf,dc=edu';

    /**
     * User source configuration
     * @var array
     */
    protected $_config;

    /**
     * Internal LDAP client which is used to connect to EDS
     * @var Ilios2_Ldap
     */

    protected $_ldap;

    /**
     * Constructor
     * @param array $config
     * @see Ilios2_UserSync_UserSource::__construct()
     */
    public function __construct(array $config = array())
    {
        $this->_config = $config;
    }

	/**
	 * Retrieves a list containing all students records from EDS.
	 * @return Ilios2_UserSync_ExternalUser_Iterator_Ldap
	 * @see Ilios2_UserSync_UserSource::getAllStudentRecords()
	 * @throws Ilios2_UserSync_Exception
	 */
	public function getAllStudentRecords ()
	{
        return $this->getStudentRecords();
	}

	/**
	 * Retrieves a list of students from EDS.
	 * @param int $limit
	 * @return Ilios2_UserSync_ExternalUser_Iterator_Ldap
	 */
	public function getStudentRecords ($limit = 0)
	{
	    // NOTE:
	    // This search filter is something of a "best guess".
	    // The value of the student registration code attribute is not reliable,
	    // so we check for the mere presence of it.
	    $filter =<<<EOL
(&(objectClass=person)
  (eduPersonAffiliation=student)
  (ucsfEduStuRegistrationStatusCode=*)
)
EOL;
        return $this->_search($filter, $limit);
	}


	/**
	 * @param string $email
	 * @return Ilios2_UserSync_ExternalUser_Iterator_Ldap
	 * @see Ilios2_UserSync_UserSource::getUserByEmail()
	 * @throws Ilios2_UserSync_Exception
	 */
	public function getUserByEmail ($email)
	{
	    $filter =<<<EOL
(&(objectClass=person)
  (mail={$email})
)
EOL;
        return $this->_search($filter);
	}

	/**
	 * @param string $uid
	 * @return Ilios2_UserSync_ExternalUser_Iterator_Ldap
	 * @see Ilios2_UserSync_UserSource::getUserByUid()
	 * @throws Ilios2_UserSync_Exception
	 */
	public function getUserByUid ($uid)
	{
	    $filter =<<< EOL
(&(objectClass=person)
  (ucsfEduIDNumber={$uid})
)
EOL;
        return $this->_search($filter);
	}

	/**
	 * @param string $uid
	 * @return boolean
     * @see Ilios2_UserSync_UserSource::hasStudent()
     */
    public function hasStudent ($uid)
    {
	    $filter =<<<EOL
(&(objectClass=person)
  (ucsfEduIDNumber={$uid})
  (eduPersonAffiliation=student)
  (ucsfEduStuRegistrationStatusCode=*)
)
EOL;
	    $result = $this->_search($filter);
        return (boolean) count($result);
    }

	/**
	 * @param string $uid
	 * @return boolean
     * @see Ilios2_UserSync_UserSource::hasUser()
     */
    public function hasUser ($uid)
    {
	    $filter =<<<EOL
(&(objectClass=person)
  (ucsfEduIDNumber={$uid})
)
EOL;
	    $result = $this->_search($filter);
        return (boolean) count($result);
    }


	/**
	 * Performs an LDAP search against EDS.
	 * @param string $filter
	 * @param int $limit number of records to return, 0 indicates 'unlimited' (this is the default)
	 * @return Ilios2_UserSync_ExternalUser_Iterator_Ldap
	 * @throws Ilios2_UserSync_Exception
	 */
	protected function _search ($filter, $limit = 0)
	{
		try {
		   $ldap = $this->getLdap();
		   $result = $ldap->search(self::EDS_BASE_DN, trim($filter), null, array(), 0, $limit);
		   $ldapIterator = new Ilios2_Ldap_Iterator($ldap, $result);
		   return new Ilios2_UserSync_ExternalUser_Iterator_Ldap($ldapIterator, new Ilios2_UserSync_ExternalUser_Factory_Eds());
		} catch (Ilios2_Ldap_Exception $e) {
		    // re-throw ldap exception
		    throw new Ilios2_UserSync_Exception("Failed to search external user source: {$e->getMessage()}",
		                Ilios2_UserSync_Exception::USER_SOURCE_ERROR);
		}
	}

	/**
	 * Returns the internal LDAP client.
	 * @return Ilios2_Ldap
	 * @throws Ilios2_Ldap_Exception
	 */
	public function getLdap ()
	{
	    if (is_null($this->_ldap)) { // lazy initialization
	        $this->_ldap = new Ilios2_Ldap($this->_config['ldap']);
	    }
	    return $this->_ldap;

	}
}
