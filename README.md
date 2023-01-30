![Symfony](https://img.shields.io/badge/symfony-%23000000.svg?style=for-the-badge&logo=symfony&logoColor=white)
![PhpStorm](https://img.shields.io/badge/phpstorm-143?style=for-the-badge&logo=phpstorm&logoColor=black&color=black&labelColor=darkorchid)

# Password Meter Symfony Bundle
PasswordMeter is a php equivalent clone of [HamedFathi/PasswordMeter](https://github.com/HamedFathi/PasswordMeter) for javascript.

## Installation
Install the package with:
```console
composer require evotodi/password-meter-bundle
```

## Usage
This bundle provides a single service to generate a password score like [passwordmeter.com](http://www.passwordmeter.com/) and [HamedFathi/PasswordMeter](https://github.com/HamedFathi/PasswordMeter).

## Configuration
```yaml
# config/packages/evotodi_password_meter.yaml
evotodi_password_meter:
    # Custom password requirements provider class
    requirements_provider: null
    
    # Custom password score range provider class
    score_provider: null

```

## Implementing password requirements
The default requirements are null and will only return a score and status.  
Creating a custom requirements provider will give and array of errors that match your custom requirments. 

First create a class that implements ```Evotodi\PasswordMeterBundle\RequirementsInterface``` and implement the ```getRequirements``` method.
From the ```getRequirements``` method return a new ```Evotodi\PasswordMeterBundle\Models\Requirements``` with your desired password requirements.
```php
// src/Service/PasswordMeterRequirementsProvider.php

namespace App\Service;

use Evotodi\PasswordMeterBundle\Interfaces\RequirementsInterface;
use Evotodi\PasswordMeterBundle\Models\Requirements;

class PasswordMeterRequirementsProvider implements RequirementsInterface
{

	public function getRequirements(): Requirements
	{
		return new Requirements(minLength: 10);
	}
}
```
Then set the following config. You may need to create the config file if it does not exist.
```yaml
# config/packages/evotodi_password_meter.yaml
evotodi_password_meter:
    requirements_provider: App\Service\PasswordMeterRequirementsProvider
```

## Implementing custom password score range
Create a class that implements ```Evotodi\PasswordMeterBundle\ScoreRangeInterface``` and implement the ```getScoreRange``` method.
From the ```getScoreRange``` method return a new array of score ranges.
```php
// src/Service/PasswordMeterScoreProvider.php

namespace App\Service;

use Evotodi\PasswordMeterBundle\Interfaces\ScoreRangeInterface;

class PasswordMeterScoreProvider implements ScoreRangeInterface
{

	public function getScoreRange(): array
	{
		return [
            '40' => 'veryWeak', // 001 <= x <  040
            '80' => 'weak', // 040 <= x <  080
            '120' => 'medium', // 080 <= x <  120
            '180' => 'strong', // 120 <= x <  180
            '200' => 'veryStrong', // 180 <= x <  200
            '_' => 'perfect', //  >= 200
        ];
	}
}
```
The array must contain at least 2 elements and the last element key must be ```'_'```.  

Then set the following config. You may need to create the config file if it does not exist.
```yaml
# config/packages/evotodi_password_meter.yaml
evotodi_password_meter:
    score_provider: App\Service\PasswordMeterScoreProvider
```

## Contributions
Contributions are very welcome! 

Please create detailed issues and pull requests.  

## Licence
This package is free software distributed under the terms of the [MIT license](LICENSE).

## Updates
2023-01-30: Initial release