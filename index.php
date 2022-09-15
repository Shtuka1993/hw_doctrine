<?php

use Doctrine\Common\ClassLoader,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\Cache\ArrayCache,
    Doctrine\DBAL\Logging\EchoSQLLogger,
    Doctrine\ORM\Mapping\Driver\DatabaseDriver,
    Doctrine\ORM\Tools\DisconnectedClassMetadataFactory,
    Doctrine\ORM\Tools\EntityGenerator;
 
/**
 * CodeIgniter Smarty Class
 *
 * initializes basic doctrine settings and act as doctrine object
 *
 * @final   Doctrine 
 * @category    Libraries
 * @author  Md. Ali Ahsan Rana
 * @link    http://codesamplez.com/
 */
class Doctrine {
 
  /**
   * @var EntityManager $em 
   */
    public $em = null;
 
  /**
   * constructor
   */
  public function __construct()
  {
    // load database configuration from CodeIgniter
    require APPPATH.'config/database.php';
     
    // Set up class loading. You could use different autoloaders, provided by your favorite framework,
    // if you want to.
    require_once APPPATH.'third_party/Doctrine/Common/ClassLoader.php';
 
    $doctrineClassLoader = new ClassLoader('Doctrine',  APPPATH.'third_party');
    $doctrineClassLoader->register();
    $entitiesClassLoader = new ClassLoader('models', rtrim(APPPATH, "/" ));
    $entitiesClassLoader->register();
    $proxiesClassLoader = new ClassLoader('proxies', APPPATH.'models');
    $proxiesClassLoader->register();
 
    // Set up caches
    $config = new Configuration;
    $cache = new ArrayCache;
    $config->setMetadataCacheImpl($cache);
    $driverImpl = $config->newDefaultAnnotationDriver(array(APPPATH.'models/Entities'));
    $config->setMetadataDriverImpl($driverImpl);
    $config->setQueryCacheImpl($cache);
 
    // Proxy configuration
    $config->setProxyDir(APPPATH.'models/proxies');
    $config->setProxyNamespace('Proxies');
 
    // Set up logger
    //$logger = new EchoSQLLogger;
    //$config->setSQLLogger($logger);
 
    $config->setAutoGenerateProxyClasses( TRUE );   
    // Database connection information
    $connectionOptions = array(
        'driver' => 'pdo_mysql',
        'user' =>     $db['default']['username'],
        'password' => $db['default']['password'],
        'host' =>     $db['default']['hostname'],
        'dbname' =>   $db['default']['database']
    );
 
    // Create EntityManager
    $this->em = EntityManager::create($connectionOptions, $config);   
   
     
  } 
}

//________________________________________________________________

/**
   * generate entity objects automatically from mysql db tables
   * @return none
   */
  function generate_classes(){     
       
    $this->em->getConfiguration()
             ->setMetadataDriverImpl(
                new DatabaseDriver(
                        $this->em->getConnection()->getSchemaManager()
                )
    );
 
    $cmf = new DisconnectedClassMetadataFactory();
    $cmf->setEntityManager($this->em);
    $metadata = $cmf->getAllMetadata();     
    $generator = new EntityGenerator();
     
    $generator->setUpdateEntityIfExists(true);
    $generator->setGenerateStubMethods(true);
    $generator->setGenerateAnnotations(true);
    $generator->generate($metadata, APPPATH."models/Entities");
     
  }

  //__________________________________________________________

  /**
 * PdContact
 *
 * @Table(name="pd_contact")
 * @Entity
 */
class PdContact
{
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;
 
    /**
     * @var string $name
     *
     * @Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;
 
    /**
     * @var string $email
     *
     * @Column(name="email", type="string", length=50, nullable=false)
     */
    private $email;
 
    /**
     * @var string $subject
     *
     * @Column(name="subject", type="string", length=100, nullable=false)
     */
    private $subject;
 
    /**
     * @var text $message
     *
     * @Column(name="message", type="text", nullable=false)
     */
    private $message;
 
 
    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }
 
    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
 
    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }
 
    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
 
    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }
 
    /**
     * Set subject
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }
 
    /**
     * Get subject
     *
     * @return string $subject
     */
    public function getSubject()
    {
        return $this->subject;
    }
 
    /**
     * Set message
     *
     * @param text $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
 
    /**
     * Get message
     *
     * @return text $message
     */
    public function getMessage()
    {
        return $this->message;
    }
}

//______________________________________________________________

require_once(APPPATH."models/Entities/PdContact.php");
use \PdContact;
/**
 * manipulates data and contains data access logics for Enity 'User'
 *
 * @final Homemodel
 * @category models 
 * @author Md. Ali Ahsan Rana
 * @link http://codesamplez.com
 */
class Homemodel extends CI_Model {
     
    /**     
     * @var \Doctrine\ORM\EntityManager $em 
     */
    var $em;
     
    public function __construct() {
        parent::__construct();
        $this->em = $this->doctrine->em;
    }
     
    /**
     * Add contact messages to database
     * @param array $contact_form
     * @return bool 
     */
    function add_message()
    {    
        /**
         * @var PdContact $contact
         */
        $contact = new PdContact();
        $contact->setName($this->input->post("name");
        $contact->setEmail($this->input->post("email");
        $contact->setSubject($this->input->post("subject");
        $contact->setMessage($this->input->post("message");
         
        try {
            //save to database
            $this->em->persist($contact);
            $this->em->flush();
        }
        catch(Exception $err){
             
            die($err->getMessage());
        }
        return true;        
    }
}

//__________________________________________________________________

$system_path = 'system';
$application_folder = 'application';
define('BASEPATH', str_replace("\\", "/", $system_path));
define('APPPATH', $application_folder.'/');
         
include __DIR__."/vendor/autoload.php";
include __DIR__."/application/libraries/doctrine.php";
 
$doctrine = new Doctrine();
$em = $doctrine->em;
$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
));
 
return $helperSet;

//_________________________________________________________________

function get_single($id)
    {
        try
        {     
            $city = $this->em->find("PdContact",$id);
            return $city;
        }
        catch(Exception $err)
        {
            return NULL;
        }
    }

//_________________________________________________________________

/**
    * Return list of records according to given start index and length
    * @param Int $start the start index number for the result entity list
    * @param Int $length Determines how many records to fetch
    * @param Array $criteria specify where conditions
    * @param String $orderby specify columns, in which data should be ordered
    * @return type 
    */
    function get_by_range($start=0,$length=10,$criteria = NULL,$orderby=NULL)
    {
        try
        {
            return $this->em->getRepository("PdContact")->findBy($criteria, $orderby, $length, $start);
        }
        catch(Exception $err)
        {
            return NULL;
        }
    }

//_________________________________________________________________

/**
 * Return the number of records
 * @return integer 
 */
function get_count()
{
    try
    {
        $query = $this->em->createQueryBuilder()
                        ->select("count(a)")
                        ->from("PdContact", "a")
                        ->getQuery();
        return $query->getSingleScalarResult();
    }
    catch(Exception $err)
    {
        return 0;
    }
}

//________________________________________________________________

/**
 * Delete an Entity according to given (list of) id(s)
 * @param type $ids array/single
 */
function delete_entities($ids){
    try
    {
        if(!is_array($ids))
        {
            $ids = array($ids);
        }
        foreach($ids as $id)
        {
            $entity = $this->em->getPartialReference("PdContact", $id);
            $this->em->remove($entity);
        }
        $this->em->flush();
        return TRUE;
    }
    catch(Exception $err)
    {
        return FALSE;
    }
}

?>