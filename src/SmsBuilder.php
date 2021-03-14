<?php

namespace App;

use smpp\{ Address, SMPP, Client as SmppClient, transport\Socket};
use Symfony\Component\Routing\Annotation\Route;

class SmsBuilder
{
    const DEFAULT_SENDER = 'github_example';
    protected $transport;
    protected $smppClient;
    protected $debug = false;
    protected $from;
    protected $login;
    protected $password;

    /**
     * smsBuilder constructor.
     * @param string $address SMSC IP
     * @param int $port SMSC port
     * @param string $login
     * @param string $password
     * @param int $timeout timeout of reading PDU in milliseconds
     * @param bool $debug - debug flag when true output additional info
     */
    public function __construct(
        string $address,
        int $port,
        string $login,
        string $password,
        int $timeout = 10000,
        bool $debug = false
    )
    {
        $this->transport = new Socket([$address], $port);
        $this->transport->setRecvTimeout($timeout);
        $this->smppClient = new SmppClient($this->transport);

        // Activate binary hex-output of server interaction
        $this->smppClient->debug = $debug;
        $this->transport->debug = $debug;

        $this->login = $login;
        $this->password = $password;

        $this->from = new Address(self::DEFAULT_SENDER,SMPP::TON_ALPHANUMERIC);
    }

    public function sendMessage()
    {
        $this->transport->open();
        $this->smppClient->bindTransceiver($this->login, $this->password);
        // strongly recommend use SMPP::DATA_CODING_UCS2 as default encoding in project to prevent problems with non latin symbols
        
        $messages = MessageRepository::getAll();
        
        foreach ($messages as $message) {
            $this->smppClient->sendSMS($this->from, $message['phone'], $message['message'], null, SMPP::DATA_CODING_UCS2);
        }
        
        $this->smppClient->close();
    }
}