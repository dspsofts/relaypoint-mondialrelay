<?php

namespace RelayPoint\MondialRelay\Test;

use RelayPoint\Core\OpeningHours;
use RelayPoint\MondialRelay\OpeningHoursParser;

class OpeningHoursParserTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Parser object.
	 *
	 * @var OpeningHoursParser
	 */
	protected $openingHoursParser;

	public function setUp()
	{
		parent::setUp();
		$this->openingHoursParser = new OpeningHoursParser();
	}

	public function testParse()
	{
		$relayPoint = new \stdClass();
		$relayPoint->Horaires_Lundi = new \stdClass();
		$relayPoint->Horaires_Lundi->string = array('0800', '1900');

		$relayPoint->Horaires_Mardi = new \stdClass();
		$relayPoint->Horaires_Mardi->string = array('0800', '1900');

		$relayPoint->Horaires_Mercredi = new \stdClass();
		$relayPoint->Horaires_Mercredi->string = array('0800', '1900');

		$relayPoint->Horaires_Jeudi = new \stdClass();
		$relayPoint->Horaires_Jeudi->string = array('0800', '1900');

		$relayPoint->Horaires_Vendredi = new \stdClass();
		$relayPoint->Horaires_Vendredi->string = array('0800', '1900');

		$relayPoint->Horaires_Samedi = new \stdClass();
		$relayPoint->Horaires_Samedi->string = array('0800', '1900');

		$relayPoint->Horaires_Dimanche = new \stdClass();
		$relayPoint->Horaires_Dimanche->string = array('0800', '1900');

		$actual = $this->openingHoursParser->parse($relayPoint);

		$expected = array(
			'Lundi' => '08:00 - 19:00',
			'Mardi' => '08:00 - 19:00',
			'Mercredi' => '08:00 - 19:00',
			'Jeudi' => '08:00 - 19:00',
			'Vendredi' => '08:00 - 19:00',
			'Samedi' => '08:00 - 19:00',
			'Dimanche' => '08:00 - 19:00',
		);

		$this->assertEquals($expected, $actual);
	}
}

