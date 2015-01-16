<?php

/**
 * Mondial Relay Gateway
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 2015-01-15
 */

namespace RelayPoint\MondialRelay;

use RelayPoint\Core\AbstractGateway;
use RelayPoint\Core\Address;
use RelayPoint\Core\RelayPointException;

class Gateway extends AbstractGateway
{
    const URL = 'http://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL';

    const SERVICE = 'WSI3_PointRelais_Recherche';

    /**
     * SOAP client for communication with Mondial Relay webservices
     *
     * @var \SoapClient
     */
    protected $soapClient;

    /**
     * Parser for the opening hours.
     *
     * @var OpeningHoursParser
     */
    protected $openingHoursParser;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->soapClient = new \SoapClient(self::URL);
        $this->openingHoursParser = new OpeningHoursParser();
    }

    /**
     * Defines the SoapClient class which must be used.
     *
     * @param \SoapClient $soapClient SoapClient instance
     */
    public function setSoapClient(\SoapClient $soapClient)
    {
        $this->soapClient = $soapClient;
    }

    public function getName()
    {
        return 'MondialRelay';
    }

    public function getDefaultParameters()
    {
        return array(
            'country' => 'FR',
        );
    }

    public function getLogin()
    {
        return $this->getParameter('login');
    }

    public function setLogin($login)
    {
        $this->setParameter('login', $login);
    }

    public function getPrivateKey()
    {
        return $this->getParameter('privateKey');
    }

    public function setPrivateKey($privateKey)
    {
        $this->setParameter('privateKey', $privateKey);
    }

    /**
     * Finds the list of relay points.
     *
     * @param array $parameters Search fields
     * @param boolean $active Turn to false if you only want active relay points
     * @return Address[]
     * @throws RelayPointException
     */
    public function search(array $parameters, $active = true)
    {
        $fields = array_replace($this->getParameters(), $parameters);

        $args = new \stdClass();
        $args->Enseigne = $fields['login'];
        $args->Pays = $fields['country'];
        $args->CP = $fields['zip'];
        $args->Poids = $fields['weight'];
        $args->Action = $fields['serviceCode'];

        $args->Security = strtoupper(
            md5(
                $fields['login']
                . $fields['country']
                . $fields['zip']
                . $fields['weight']
                . $fields['serviceCode']
                . $fields['privateKey']
            )
        );

        $result = $this->soapClient->WSI3_PointRelais_Recherche($args);

        if (!isset($result->WSI3_PointRelais_RechercheResult->PointsRelais->PointRelais_Details)) {
            $result = array();
        } else {
            $result = $result->WSI3_PointRelais_RechercheResult->PointsRelais->PointRelais_Details;
            if (!is_array($result)) {
                $result = array($result);
            }
        }

        $list = array();

        foreach ($result as $relayPoint) {
            $address = $this->parseRelayPoint($relayPoint);

            $list[] = $address;
        }

        return $list;
    }

    /**
     * Returns the details of one Chronorelais relay point.
     *
     * @param array $parameters Search fields
     * @return Address|null
     * @throws RelayPointException
     */
    public function detail(array $parameters)
    {
        $fields = array_replace($this->getParameters(), $parameters);

        $args = new \stdClass();
        $args->Enseigne = $fields['login'];
        $args->Pays = $fields['country'];
        $args->NumPointRelais = $fields['code'];
        $args->Poids = $fields['weight'];
        $args->Action = $fields['serviceCode'];

        $args->Security = strtoupper(
            md5(
                $fields['login']
                . $fields['country']
                . $fields['code']
                . $fields['weight']
                . $fields['serviceCode']
                . $fields['privateKey']
            )
        );

        $result = $this->soapClient->WSI3_PointRelais_Recherche($args);

        return $this->parseRelayPoint($result->WSI3_PointRelais_RechercheResult->PointsRelais->PointRelais_Details);
    }

    /**
     * Parse the SOAP result of a relay point and returns the result in an Address object.
     *
     * @param \stdClass $relayPoint Relay point
     * @return Address
     */
    private function parseRelayPoint(\stdClass $relayPoint)
    {
        $optionalFields = array(
            'adresse2',
            'adresseAutre',
            'urlPlan',
        );

        $fields = array(
            'active' => true,
            'code' => trim($relayPoint->Num),
            'name' => trim($relayPoint->LgAdr1),
            'street' => trim($relayPoint->LgAdr3),
            'zip' => trim($relayPoint->CP),
            'city' => trim($relayPoint->Ville),
            'locationHint' => trim(trim($relayPoint->Localisation1) . ' ' . trim($relayPoint->Localisation2)),
            'longitude' => $this->getOptionalField($relayPoint, 'Longitude'),
            'latitude' => $this->getOptionalField($relayPoint, 'Latitude'),
            'image' => $this->getOptionalField($relayPoint, 'URL_Photo'),
            'adresse2' => $this->getOptionalField($relayPoint, 'LgAdr4'),
            'adresseAutre' => $this->getOptionalField($relayPoint, 'LgAdr2'),
            'urlPlan' => $this->getOptionalField($relayPoint, 'URL_Plan'),
        );

        $fields['longitude'] = str_replace(',', '.', $fields['longitude']);
        $fields['latitude'] = str_replace(',', '.', $fields['latitude']);

        foreach ($optionalFields as $fieldName) {
            if ($fields[$fieldName] == '') {
                unset($fields[$fieldName]);
            }
        }

        $address = new Address($fields);

        $hours = $this->openingHoursParser->parse($relayPoint);

        foreach ($hours as $day => $hour) {
            $address->addOpeningHour($day, $hour);
        }

        return $address;
    }

    /**
     * Returns asked field or an empty string if it isn't set.
     *
     * @param \stdClass $relayPoint Relay point
     * @param string $fieldName Field name
     * @return string
     */
    private function getOptionalField($relayPoint, $fieldName)
    {
        if (isset($relayPoint->{$fieldName})) {
            return trim($relayPoint->{$fieldName});
        }
        return '';
    }
}
