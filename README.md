## About Covid19 API

Covid19 API was built using Laravel 7. Built to service front-end frameworks. 
Data is from [John Hopkins CSSE](https://github.com/CSSEGISandData/COVID-19).
Data is updated DAILY.

- [GitLab - Vue Front-end implementation](https://gitlab.com/dev.weward/vue-larasavings).
- [Github - Vue Front-end implementation](https://github.com/weward/vue-larasavings).

## Installation

- Clone: `git clone https://gitlab.com/dev.weward/api-covid19.git`
- Or Clone from Github: `git clone https://github.com/weward/api-covid.git`
- Create .env file
- then issue `composer install`
- `php artisan key:generate`
- `composer dump-autoload`
- `php artisan config:clear`
- `php artisan cache:clear`
- `php artisan route:clear`

## Sample JSON Response Format

````
[
    {
        "province": "",
        "country": "Thailand",
        "confirmed": 10,
        "death": 3,
        "recovered": 1,
        "mortality_rate": "0.33",
        "recovery_rate": "10"
    },
    {
        "province": "Hubei",
        "country": "China",
        "confirmed": 100,
        "death": 15,
        "recovered": 20,
        "mortality_rate": "15",
        "recovery_rate": "20"
    },
]
````