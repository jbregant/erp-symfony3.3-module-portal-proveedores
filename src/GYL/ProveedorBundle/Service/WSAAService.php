<?php

namespace GYL\ProveedorBundle\Service;

use Symfony\Component\DependencyInjection\SimpleXMLElement;
use Symfony\Component\HttpKernel\Exception\HttpNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bridge\Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

/**
 * Description of WSAAService
 * 
 */
class WSAAService extends \SoapClient {
    
    private $signedTRA;
    private $status;
    private $Token;
    private $Sign;
    private $WSAA_WSDL_URL;
    private $WSAA_URL;
    private $APP_PATH;
    private $PRIV_KEY;
    private $CERT;
    private $PASSPHRASE;
    private $logger;
    private $doctrine;
    
    public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container, $authService) 
    {
        $this->container = $container;
        $this->doctrine = $container->get('doctrine');
        $this->authService = $authService;
        $this->initLogger();
        $status = $this->container->get('kernel')->getEnvironment();
        $this->status = ($status == 'dev')?'test':$status;

        $this->APP_PATH = dirname($this->container->getParameter('kernel.root_dir'));
        $this->WSAA_WSDL_URL = $this->container->getParameter('wsaa_' . $status . '.WSAA_WSDL_URL');
        $this->WSAA_URL = $this->container->getParameter('wsaa_' . $status . '.WSAA_URL');
        $this->PASSPHRASE = '';
        $this->PRIV_KEY =  $this->APP_PATH . $this->container->getParameter($this->authService. '_' . $status . '.PRIV_KEY');
        $this->CERT = $this->APP_PATH . $this->container->getParameter( $this->authService . '_' . $status .'.CERT');
        
        $this->APP_PATH .= $this->container->getParameter( $this->authService . '_' . $status .'.DIR');
        
        $scOpts = array(
            'soap_version' => SOAP_1_2,
            'location' => $this->WSAA_URL,
            'trace' => 1,
            'exceptions' => true
        );

        parent::SoapClient($this->WSAA_WSDL_URL, $scOpts);
        
    }

    private function createTRA() {
        $TRA = new \SimpleXMLElement(
                '<?xml version="1.0" encoding="UTF-8"?>' .
                '<loginTicketRequest version="1.0">' .
                '</loginTicketRequest>');
        $TRA->addChild('header');
        $TRA->header->addChild('uniqueId', date('U'));
        $TRA->header->addChild('generationTime', date('c', date('U') - 600));
        $TRA->header->addChild('expirationTime', date('c', date('U') + 600));
        $TRA->addChild('service', $this->authService);
        $TRA->asXML($this->APP_PATH . 'TRA.xml');
    }

    private function signTRA() {
        $STATUS = openssl_pkcs7_sign($this->APP_PATH . 'TRA.xml', $this->APP_PATH . 'TRA.tmp', 'file://' . $this->CERT, 
        array('file://' . $this->PRIV_KEY, $this->PASSPHRASE), 
        array(), !PKCS7_DETACHED
        );

        if (!$STATUS) {
            exit("ERROR generating PKCS#7 signature\n");
        }

        //Reflejo 10/08/2018 creo que se olvidaron un / al menos en local funciona asi.
        $inf = fopen($this->APP_PATH . "TRA.tmp", "r");
        $i = 0;
        $CMS = "";

        while (!feof($inf)) {
            $buffer = fgets($inf);

            if ($i++ >= 4)
                $CMS.=$buffer;
        }

        fclose($inf);
        //Reflejo 10/08/2018 creo que se olvidaron un / al menos en local funciona asi.
        unlink($this->APP_PATH . "TRA.tmp");
        //Reflejo 10/08/2018 creo que se olvidaron un / al menos en local funciona asi.
        unlink($this->APP_PATH . "TRA.xml");
        $this->signedTRA = $CMS;
    }

    private function callWSAA() {
        $loginCms = $this->getLoginCms();
        // Si existe el archivo con la respuesta, tomar los datos para evaluar validez
        if ($loginCms != null) {          
            $xmlResponse = simplexml_load_string($loginCms);
            $respDate = $xmlResponse->header->expirationTime;
            $pattern = "/(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2}:\d{2}\.\d{3})(.*)/";
            preg_match($pattern, $respDate, $arrExpDate);
            $expDate = $arrExpDate[1] . ' ' . $arrExpDate[2] . ' ' . $arrExpDate[3];
            $strExpDate = strtotime($expDate);

            $now = date('Y-m-d H:i:s P');
            $strNow = strtotime($now);
        }

        // Si el ticket expiro, generarlo nuevamente
        if ((!isset($strExpDate)) || (!isset($strNow)) || ($strNow > $strExpDate)) {

            $results = $this->loginCms(array('in0' => $this->signedTRA));
            
            $this->logger->info('-------loginCmsReturn-------------');
            $this->logger->info($results->loginCmsReturn);
            $this->logger->info('----------------------------------');

            $this->setLoginCms($results->loginCmsReturn);
            $xmlResponse = new \SimpleXMLElement($results->loginCmsReturn);

            if (is_soap_fault($results)) {
                return ("SOAP Fault: " . $results->faultcode . "\n" . $results->faultstring . "\n");
            }
        } 

        $this->Token = $xmlResponse->credentials->token;
        $this->Sign = $xmlResponse->credentials->sign;

        return 0;
    }

    public function getAuth() {
        $this->createTRA();
        $this->signTRA();
        $this->callWSAA();

        return array('Token' => $this->Token, 'Sign' => $this->Sign);
    }
    
    private function initLogger() {
        $this->logger = new Logger('WSAA');
        $monologFormat = "%message%\n";
        $dateFormat = "Y/m/d H:i:s";
        $monologLineFormat = new LineFormatter($monologFormat, $dateFormat);
        $streamHandler = new StreamHandler($this->container->get('kernel')->getRootDir() . '/logs/auth/WSAA_' . $this->authService . '_' . date('Y_m_d__H_i_s') . '.log', Logger::INFO);
        $streamHandler->setFormatter($monologLineFormat);
        $this->logger->pushHandler($streamHandler);
    }
    
    public function getLoginCms(){
        $sql = "SELECT login_cms FROM web_service WHERE id_empresa = 1 AND denominacion = 'WSSA' AND ambiente = '$this->status'";
        $emRrhh = $this->doctrine->getManager('siga_autenticacion');
        $stmt = $emRrhh->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function setLoginCms($loginCms){
        $sql = "UPDATE web_service SET login_cms = :login_cms WHERE id_empresa = 1 AND denominacion = 'WSSA' AND ambiente = '$this->status'";
        $emRrhh = $this->doctrine->getManager('siga_autenticacion');
        $stmt = $emRrhh->getConnection()->prepare($sql);
        $stmt->bindValue(":login_cms", $loginCms);
        $count = $stmt->execute();
        
        return ($count > 0);
    }
}