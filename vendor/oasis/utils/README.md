# Utilities for Oasis projects

This component provides a number of PHP helper classes for common tasks.

- [Data Provider](#data-provider)
- [Streamed Data Packer](#data-packer)
- [Caesar Cipher](#caesar-cipher)
- [RC4 encryption/decryption](#rc4)
- [String Utilities](#string-utils)
- [Memory Monitor Tool](#memory-usage-monitor)

### Installation

Install the latest version with command below:

```bash
$ composer require oasis/utils
```

### Data Provider

Normally we use data provider to create a validatable container.
`Oasis\Mlib\Utils\ArrayDataProvider` is probably the most used.

An example:

```php
<?php

use Oasis\Mlib\Utils\ArrayDataProvider;
use Oasis\Mlib\Utils\DataProviderInterface;

$data = [
    "int-key" => 10,
    "string-key" => "name",
    "switch" => "true",
    "wrong-type" => "hello",
];

$dp = new ArrayDataProvider($data);

// $i = 10
$i = $dp->getMandatory('int-key', DataProviderInterface::INT_TYPE);
// throws Oasis\Mlib\Utils\Exceptions\MandatoryValueMissingException
$i = $dp->getMandatory('int-key2', DataProviderInterface::INT_TYPE);
// $i = 5
$i = $dp->getOptional('int-key2', DataProviderInterface::INT_TYPE, 5);

// $s = 'name';
$s = $dp->getMandatory('string-key', DataProviderInterface::STRING_TYPE);

// $onoff = true;
$onoff = $dp->getOptional('switch', DataProviderInterface::BOOL_TYPE, false);

// throws Oasis\Mlib\Utils\Exceptions\InvalidDataTypeException
$wrongType = $dp->getMandatory('wrong-type', DataProviderInterface::BOOL_TYPE);

```

### Data Packer

When you have some objects that should be write to or read from a stream
(file, network), `Oasis\Mlib\Utils\DataPacker` is just for you.

Below is an example using Data Packer:

```php
<?php

use Oasis\Mlib\Utils\DataPacker;

$obj = new stdClass();

$tmpfile = tempnam(sys_get_temp_dir(), '');

$packer = new DataPacker();
$fh     = fopen($tmpfile, 'w');
$packer->attachStream($fh);
$packer->packToStream($obj);
$packer->packToStream($obj);
$packer->packToStream($obj);
fclose($fh);

$fh = fopen($tmpfile, 'r');
$packer->attachStream($fh);
while ($obj = $packer->unpackFromStream()) {
    // we should have 3 $obj unpacked from stream
}

```

### Caesar Cipher

[Caesar Cipher] is one of the earliest known and simplest ciphers. It is a type of substitution cipher in which each letter in the plaintext is 'shifted' a certain number of places down the alphabet.

**oasis/utils** provides easy cipher class for Caesar Cipher:

```php
<?php
use Oasis\Mlib\Utils\CaesarCipher;

$cipher = new CaesarCipher();

// encrypt and decrypt integer
$enc = $cipher->encrypt(1234);
$dec = $cipher->decrypt($enc); // $dec = 1234

// encrypt and decrypt string
$enc = $cipher->encrypt("abcdefg");
$dec = $cipher->decrypt($enc); // $dec = "abcdefg"

// using stronger cipher
$cipher = new CaesarCipher(
    32, // bits to use in partition, default to 32, must be divisable by partition size
    8,  // partition size, even positive number, default to 8
    12  // strength, positive number, default to 5
);

```

### RC4

[RC4] is a very simple stream cipher. **oasis/utils** provides quick
access to [RC4] encryption. Below is an example:

```php
<?php

use Oasis\Mlib\Utils\Rc4;

$encrypted = Rc4::rc4('random-key', 'abc');

// to decrypt, call the encrypt function with same key
$decrypted = Rc4::rc4('random-key', $encrypted); // $decrypted = 'abc'

```

### String Utils

`Oasis\Mlib\Utils\StringUtils` class provides some simple string related functions:

```php
<?php

use Oasis\Mlib\Utils\StringUtils;

$str = 'abcdefg';

var_dump(StringUtils::stringStartsWith($str, 'a')); // true
var_dump(StringUtils::stringStartsWith($str, 'b')); // false
var_dump(StringUtils::stringEndsWith($str, 'g')); // true
var_dump(StringUtils::stringEndsWith($str, 'f')); // false

var_dump(StringUtils::stringChopdown($str, 4)); // 'abcd'

```

### Memory Usage Monitor

Sometimes, PHP script can run out of memory. It is especially important to monitor
memory usage is a long-running script. [oasis/utils]() provides some tools for
memory usage monitor and management on-the-fly.

Example:

```php
<?php

use Oasis\Mlib\Utils\CommonUtils;

// check the current memory usage and increase if needed
CommonUtils::monitorMemoryUsage();

// if the script declares tick, this will monitor memory usage every time tick is triggered
CommonUtils::registerMemoryMonitorForTick();
```

> [Tick] is a declare directive which PHP supports to monitor low-level code execution.

[Caesar Cipher]: https://en.wikipedia.org/wiki/Caesar_cipher
[RC4]: https://en.wikipedia.org/wiki/RC4
[Tick]: http://php.net/manual/en/control-structures.declare.php#control-structures.declare.ticks
