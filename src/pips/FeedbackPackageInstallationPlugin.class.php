<?php
//wcf imports
require_once(WCF_DIR.'lib/acp/package/plugin/AbstractPackageInstallationPlugin.class.php');

/**
 * This pip shows a feedback form during uninstallation.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.feedback
 * @subpackage acp.package.plugin
 * @category Community Framework
 */
class FeedbackPackageInstallationPlugin extends AbstractPackageInstallationPlugin {
    public $tagName = 'feedback';
    public $tableName = 'feedback';
    
    /**
     * Complete name of table.
     *
     * @var String
     */
    protected $completeTableName = '';
    
    /**
     * True if the template was shown yet.
     *
     * @var boolean
     */
    protected $once = false;
    
    /**
     * Contains the feedback of the user.
     *
     * @var String
     */
    protected $feedback = '';
    
    /**
     * Contains the user email.
     *
     * @var String
     */
    protected $userEmail = '';
    
    /**
     * Contains the name of the error field.
     *
     * @var String
     */
    protected $errorField = '';
    
    /**
     * Contains the name of the error type.
     *
     * @var String
     */
    protected $errorType = '';
    
    /**
     * Contains the email.
     *
     * @var String
     */
    protected $email = '';
    
    /**
     * True if the user email is optional.
     *
     * @var boolean
     */
    protected $userEmailOptional = true;
    
    /**
     * Contains the subject of the email.
     *
     * @var String
     */
    protected $subject = '';
    
    /**
     * Creates a new FeedbackPackageInstallationPlugin object.
     *
     * @param PackageInstallationQueue $installation
     */
    public function __construct(PackageInstallationQueue $installation) {
        parent::__construct($installation);
        $this->completeTableName = 'wcf'.WCF_N.'_'.$this->tableName;
        $this->readFormParameters();
        
    }
    
    /**
     * @see AbstractPackageInstallationPlugin::install()
     */
    public function install() {
        parent::install();
        $feedbackTag = $this->installation->getXMLTag($this->tagName);
        $this->email = StringUtil::trim($feedbackTag['email']);
        $this->subject = StringUtil::trim($feedbackTag['cdata']);
        if (isset($feedbackTag['userEmailOptional'])) {
            $this->userEmailOptional = (boolean) intval($feedbackTag['userEmailOptional']);
        }
        
        //checks whether this is an installation or an update
        if ($this->installation->getAction() == 'install') {
            $sql = 'INSERT INTO '.$this->completeTableName.'
        				(packageID, email, subject, userEmailOptional)
        			VALUES ('.intval($this->installation->getPackageID()).", '".
                        escapeString($this->email)."', '".escapeString($this->subject)."',
                         ".intval($this->userEmailOptional).')';
            WCF::getDB()->sendQuery($sql);
        }
        elseif ($this->installation->getAction() == 'update') {
            $sql = 'UPDATE '.$this->completeTableName."
            	SET email = '".escapeString($this->email)."', subject = '".
                        escapeString($this->subject)."',
            		userEmailOptional = ".intval($this->userEmailOptional)."
            	WHERE packageID = ".intval($this->installation->getPackageID());
            WCF::getDB()->sendQuery($sql);
        }
    }
    
    /**
     * @see AbstractPackageInstallationPlugin::uninstall()
     */
    public function uninstall() {
        $this->show();
    }
    
    /**
     * Reads the form parameters which were sent.
     */
    protected function readFormParameters() {
        if (isset($_POST['once'])) $this->once = (boolean) intval($_POST['once']);
        if (isset($_POST['feedback'])) $this->feedback = StringUtil::trim($_POST['feedback']);
        if (isset($_POST['userEmail'])) $this->userEmail = StringUtil::trim($_POST['userEmail']);
    }
    
    /**
     * Reads data from the database.
     */
    protected function readData() {
        $sql = 'SELECT email, subject, userEmailOptional
        		FROM '.$this->completeTableName.'
        		WHERE packageID = '.intval($this->installation->getPackageID());
        $row = WCF::getDB()->getFirstRow($sql);
        $this->email = StringUtil::trim($row['email']);
        $this->subject = StringUtil::trim($row['subject']);
        $this->userEmailOptional = (boolean) StringUtil::trim($row['userEmailOptional']);
        if (count($_POST)) {
            $this->readFormParameters();
            try {
                if ($this->once) {
                    $this->validate();
                    $this->sendFeedback();
                    return;
                }
            } catch (UserInputException $uie) {
                $this->errorField = $uie->getField();
                $this->errorType = $uie->getType();
                $this->once = false;
            }
        }
    }
    
    /**
     * Validates the user input.
     *
     * @throws UserInputException
     */
    protected function validate() {
        if (!$this->userEmailOptional) {
            if (empty($this->userEmail)) {
                throw new UserInputException('userEmail');
            } elseif (!$this->validateEmail($this->userEmail)) {
                throw new UserInputException('userEmail', 'notValid');
            }
        }
        if (empty($this->feedback)) {
            throw new UserInputException('feedback');
        }
    }
    
    /**
     * Assigns the variables to the template.
     */
    protected function assignVariables() {
        WCF::getTPL()->assign(
            array(
                'userEmail' => $this->userEmail,
                'feedback' => $this->feedback,
                'errorType' => $this->errorType,
                'errorField' => $this->errorField,
                'userEmailOptional' => intval($this->userEmailOptional)
            )
        );
    }
    
    /**
     * Shows the template.
     */
    protected function show() {
        $this->readData();
        $this->assignVariables();
        if (!$this->once) {
            WCF::getTPL()->display('packageFeedback');
            exit;
        }
    }
    
    /**
     * Checks whether the given email is valid or not.
     *
     * @param String $email
     */
    protected function validateEmail($email) {
        return UserUtil::isValidEmail($email);
    }
    
    /**
     * Sends feedback to the recipient saved in database.
     */
    protected function sendFeedback() {
        require_once(WCF_DIR.'lib/data/mail/Mail.class.php');
        $mail = new Mail($this->email, $this->subject, $this->feedback);
        if (!empty($this->userEmail)) {
            $header = 'Reply-To: '.$this->userEmail;
            $mail->setHeader($header);
        }
        $mail->send();
        parent::uninstall();
    }
}
