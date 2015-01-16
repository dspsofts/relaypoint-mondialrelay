<?php

/**
 * Allows to parse the opening hours of a Mondial Relay SOAP result.
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 2015-01-16
 */

namespace RelayPoint\MondialRelay;

use RelayPoint\Core\OpeningHours;

class OpeningHoursParser
{
	/**
	 * Parse the SOAP result of a relay point and returns an array of OpeningHours
	 *
	 * @param \stdClass $relayPoint Relay point
	 * @return array[OpeningHours]
	 */
	public function parse(\stdClass $relayPoint)
	{
		$days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
		$hours = array();

		foreach ($days as $day) {
			$detail = '';
			if (isset($relayPoint->{'Horaires_' . $day}) && !empty($relayPoint->{'Horaires_' . $day})) {
				$i = 0;
				foreach ($relayPoint->{'Horaires_' . $day} as $openingHours) {
					foreach ($openingHours as $openingHour) {
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
				}

				$hours[$day] = $detail;
			}

			if ($detail == '') {
				$hours[$day] = 'Fermé';
			}
		}

		return $hours;
	}
}
