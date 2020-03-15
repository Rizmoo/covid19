<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class APIController extends Controller
{
    protected $confirmed_url;
    protected $deaths_url;
    protected $recovered_url;

    public function __construct()
    {
        $this->confirmed_url = 'https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_19-covid-Confirmed.csv';
        $this->deaths_url = 'https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_19-covid-Deaths.csv';
        $this->recovered_url = 'https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_19-covid-Recovered.csv';
    }

    public function index()
    {
        $countriesAndConfirmed = [];
        $content = $this->getCountriesAndConfirmed();
        $deaths = $this->getDeaths();
        $recovered = $this->getRecovered();

        if (count($content) > 0) {
            foreach ($content as $key => &$c) {
                $c['deaths'] = $deaths[$key];
                $c['recovered'] = $recovered[$key];
                $mortality_rate = ($c['confirmed'] > 0) ? ($c['deaths'] / $c['confirmed']) * 100 : 0;
                $c['mortality_rate'] = number_format($mortality_rate, 2);
                $recovery_rate = ($c['confirmed'] > 0) ? ($c['recovered'] / $c['confirmed']) * 100 : 0;
                $c['recovery_rate'] = number_format($recovery_rate  , 2);
            }
        }

        return response()->json($content);
    }

    public function getCountriesAndConfirmed()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $this->confirmed_url);
        $content = $response->getBody()->getContents();

        $arr = [];
        $rows = explode(PHP_EOL, $content);
        $rowLength = (count($rows) - 1); // last row is deformed
        foreach ($rows as $key => $row) {
            if ($key > 0 && $key != $rowLength) {
                $columns = explode(',', $row);
                $length = count($columns) - 1; // last column returns empty string
                /**
                 * Some rows have province that contains ',' character. This affects the 
                 * explosion of columns.
                 * 
                 * "New Castle, DE" - provice
                 *  US - Country
                 * 
                 * Hunan - province
                 * China - Country
                 * 
                 * blank - province
                 * Thailand - Country
                */
                if ($columns[0] != '') {
                    // if col3 is a string, it has a ',' in province(col0)!
                    $isCol3String = !is_numeric($columns[2]);
                    // if has ',' in province
                    $province = ($isCol3String) ? str_replace('"', '', $columns[0]).", ".str_replace('"', '', $columns[1]) : str_replace('"', '', $columns[0]);
                    $country = ($isCol3String) ? str_replace('"', '', $columns[2]) : str_replace('  "', '', $columns[1]);
                    $lat = ($isCol3String) ? $columns[3] : $columns[2];
                    $long = ($isCol3String) ? $columns[4] : $columns[3];
                } else {
                    // has no province
                    $province = str_replace('"', '', $columns[0]);
                    $country = str_replace('"', '', $columns[1]);
                    $lat = $columns[2];
                    $long = $columns[3];
                }

                // Get Last Column's value
                $total = intval($columns[$length]);
                
                $arr[] = [
                  'province' => $province,
                  'country' => $country,
                  'lat' => $lat,
                  'long' => $long,
                  'confirmed' => $total,
                ];
            }
        }   

        return $arr;
    }

    public function getDeaths()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $this->deaths_url);
        $content = $response->getBody()->getContents();
        
        $arr = [];
        $rows = explode(PHP_EOL, $content);
        $rowLength = (count($rows) - 1); // last row is deformed
        foreach ($rows as $key => $row) {
            if ($key > 0 && $key != $rowLength) {
                $columns = explode(',', $row);
                $length = count($columns) - 1; // last column returns empty string
                $total = intval($columns[$length]);

                $arr[] = $total;
            }
        }

        return $arr;
    }

    public function getRecovered()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $this->recovered_url);
        $content = $response->getBody()->getContents();

        $arr = [];
        $rows = explode(PHP_EOL, $content);
        $rowLength = (count($rows) - 1); // last row is deformed
        foreach ($rows as $key => $row) {
            if ($key > 0 && $key != $rowLength) {
                $columns = explode(',', $row);
                $length = count($columns) - 1; // last column returns empty string
                $total = intval($columns[$length]);

                $arr[] = $total;
            }
        }

        return $arr;    
    }


}
