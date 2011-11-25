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
     * @var String
     */
    protected $completeTableName = '';
    
    /**
     * True if the template was shown yet.
     * @var boolean
     */
    protected $once = false;
    
    /**
     * Contains the feedback of the user.
     * @var String
     */
    protected $feedback = '';
    
    /**
     * Creates a new FeedbackPackageInstallationPlugin object.
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
        $email = StringUtil::trim($feedbackTag['email']);
        $subject = StringUtil::trim($feedbackTag['cdata']);
        if (!UserUtil::isValidEmail($email)) return; //checks whether the email is valid or not
        //checks whether this is an installation or an update
        if ($this->installation->getAction() == 'install') {
            $sql = 'INSERT INTO '.$this->completeTableName.'
        				(packageID, email, subject)
        			VALUES ('.intval($this->installation->getPackageID()).", '".
                        escapeString($email)."', '".escapeString($subject)."')";
            WCF::getDB()->sendQuery($sql);
        } elseif ($this->installatio->getAction() == 'update') {
            $sql = 'UPDATE '.$this->completeTableName."
            	SET email = '".escapeString($email)."', subject = '".escapeString($subject)."'
            	WHERE packageID = ".intval($this->installation->getPackageID());
            WCF::getDB()->sendQuery($sql);
        }
    }
    
    /**
     * @see AbstractPackageInstallationPlugin::uninstall()
     */
    public function uninstall() {
        if ($this->once) {
            $this->sendFeedback();
            parent::uninstall();
            return;
        }
        WCF::getTPL()->display('packageFeedback');
        exit;
    }
    
    /**
     * Reads the form parameters which were sent.
     */
    protected function readFormParameters() {
        if (isset($_POST['once'])) $this->once = (boolean) intval($_POST['once']);
        if (isset($_POST['feedback'])) $this->feedback = StringUtil::trim($_POST['feedback']);
    }
    
    /**
     * Sends feedback to the recipient saved in database.
     */
    protected function sendFeedback() {
        $sql = 'SELECT email, subject
        		FROM '.$this->completeTableName.'
        		WHERE packageID = '.intval($this->installation->getPackageID());
        $row = WCF::getDB()->getFirstRow($sql);
        require_once(WCF_DIR.'lib/data/mail/Mail.class.php');
        $mail = new Mail($row['email'], $row['subject'], $this->feedback);
        $mail->send();
    }
}