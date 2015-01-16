<?php

/**
 * Allows to parse the opening hours of a Mondial Relay SOAP result.
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 2015-01-16
 */

namespace RelayPoint\MondialRelay;

class OpeningHoursParser
{
	/**
	 * Parse the SOAP result of a relay point and returns an array of OpeningHours
	 *
	 * @param \stdClass $relayPoint Relay point
	 * @return array
	 */
	public function parse(\stdClass $relayPoint)
	{
		$days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
		$hours = array();

		foreach ($days as $day) {
			$hours[$day] = '';
			if (isset($relayPoint->{'Horaires_' . $day}) && !empty($relayPoint->{'Horaires_' . $day})) {
				$hours[$day] = $this->formatHours($relayPoint->{'Horaires_' . $day}->string);
			}

			if ($hours[$day] == '') {
				$hours[$day] = 'Fermé';
			}
		}

		return $hours;
	}

	/**
	 * Formats the hours based on the SOAP format.
	 *
	 * @param array $hours Hours
	 * @return string Formatted hours
	 */
	private function formatHours(array $hours)
	{
		$i = 0;
		$detail = '';
		foreach ($hours as $openingHour) {
			if ($openingHour != '0000') {
				if ($i > 0) {
					if ($i % 2 == 0) {
						$detail .= ' ';
					} else {
						$detail .= ' - ';
					}
				}
				$detail .= substr($openingHour, 0, 2) . ':' . substr($openingHour, -2);
				$i++;
			}
		}
		return $detail;
	}
}
