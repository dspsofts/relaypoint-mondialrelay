RelayPoint gateway for Mondial Relay
======

Mondial Relay relay point search

[![Latest Version](https://img.shields.io/github/release/pfeyssaguet/relaypoint-mondialrelay.svg?style=flat-square)](https://github.com/pfeyssaguet/relaypoint-mondialrelay/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/pfeyssaguet/relaypoint-mondialrelay/master.svg?style=flat-square)](https://travis-ci.org/pfeyssaguet/relaypoint-mondialrelay)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/pfeyssaguet/relaypoint-mondialrelay.svg?style=flat-square)](https://scrutinizer-ci.com/g/pfeyssaguet/relaypoint-mondialrelay/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/pfeyssaguet/relaypoint-mondialrelay.svg?style=flat-square)](https://scrutinizer-ci.com/g/pfeyssaguet/relaypoint-mondialrelay)


## Install

For now you have to clone this repository. Install via Composer might come later :)

## Usage

``` php
$o = (new GatewayFactory())->create('MondialRelay');
$o->setParameter('login', 'your login or enseigne code');
$o->setParameter('privateKey', 'your private key');

$a = $o->search(array('zip' => '75004', 'serviceCode' => '24R', 'weight' => 12000));

var_dump($a);
```

## Testing

``` bash
$ phpunit
```

## Credits

- [Pierre Feyssaguet](https://github.com/pfeyssaguet)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
