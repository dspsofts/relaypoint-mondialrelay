<?php

namespace RelayPoint\MondialRelay\Test;

use RelayPoint\MondialRelay\Gateway;

class GatewayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Gateway
     */
    protected $gateway;

    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway();
        $this->gateway->setLogin('login');
        $this->gateway->setPrivateKey('privateKey');
    }

    protected function getMockAddress()
    {
        static $mockAddress = null;
        if ($mockAddress === null) {
            $mockAddress = new \stdClass();
            $mockAddress->LgAdr1 = 'name';
            $mockAddress->LgAdr3 = 'street';
            $mockAddress->CP = 'zip';
            $mockAddress->Ville = 'city';
            $mockAddress->Num = 'code';
            $mockAddress->Latitude = 'latitude';
            $mockAddress->Longitude = 'longitude';
            $mockAddress->Localisation1 = 'locationHint';
            $mockAddress->Localisation2 = '';
            $mockAddress->URL_Plan = 'urlPlan';
            $mockAddress->URL_Photo = 'image';

            $mockAddress->Horaires_Lundi = new \stdClass();
            $mockAddress->Horaires_Lundi->string = array('0800', '1900');

            $mockAddress->Horaires_Mardi = new \stdClass();
            $mockAddress->Horaires_Mardi->string = array('0800', '1900');

            $mockAddress->Horaires_Mercredi = new \stdClass();
            $mockAddress->Horaires_Mercredi->string = array('0800', '1900');

            $mockAddress->Horaires_Jeudi = new \stdClass();
            $mockAddress->Horaires_Jeudi->string = array('0800', '1900');

            $mockAddress->Horaires_Vendredi = new \stdClass();
            $mockAddress->Horaires_Vendredi->string = array('0800', '1900');

            $mockAddress->Horaires_Samedi = new \stdClass();
            $mockAddress->Horaires_Samedi->string = array('0800', '1900');

            $mockAddress->Horaires_Dimanche = new \stdClass();
            $mockAddress->Horaires_Dimanche->string = array('0800', '1900');
        }

        return $mockAddress;
    }

    public function testSearch()
    {
        $soapClient = $this->getMockFromWsdl(__DIR__ . '/wsdl.xml', 'SoapClientMockMondialRelayForSearch');
        $this->gateway->setSoapClient($soapClient);

        $resultSearch = new \stdClass();
        $resultSearch->WSI3_PointRelais_RechercheResult = new \stdClass();

        $resultSearch->WSI3_PointRelais_RechercheResult
            ->PointsRelais = new \stdClass();

        $resultSearch->WSI3_PointRelais_RechercheResult
            ->PointsRelais
            ->PointRelais_Details = array($this->getMockAddress());

        $soapClient->expects($this->any())
            ->method(Gateway::SERVICE)
            ->will($this->returnValue($resultSearch));

        $list = $this->gateway->search(array('zip' => '75004', 'serviceCode' => '24R', 'weight' => 12000));
        $this->assertNotEmpty($list);
    }

    public function testDetail()
    {
        $soapClient = $this->getMockFromWsdl(__DIR__ . '/wsdl.xml', 'SoapClientMockMondialRelayForDetail');
        $this->gateway->setSoapClient($soapClient);

        $resultDetail = new \stdClass();
        $resultDetail->WSI3_PointRelais_RechercheResult = new \stdClass();

        $resultDetail->WSI3_PointRelais_RechercheResult
            ->PointsRelais = new \stdClass();

        $resultDetail->WSI3_PointRelais_RechercheResult
            ->PointsRelais
            ->PointRelais_Details = $this->getMockAddress();

        $soapClient->expects($this->any())
            ->method(Gateway::SERVICE)
            ->will($this->returnValue($resultDetail));

        $detail = $this->gateway->detail(array('code' => 'code', 'serviceCode' => '24R', 'weight' => 12000));

        $expected = array(
            'active' => true,
            'code' => 'code',
            'name' => 'name',
            'street' => 'street',
            'locationHint' => 'locationHint',
            'zip' => 'zip',
            'city' => 'city',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'urlPlan' => 'urlPlan',
            'image' => 'image',
        );
        $this->assertEquals($expected, $detail->getFields());
    }
}
