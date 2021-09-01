<?php
require_once(__DIR__ . "/../models/Location.php");
require_once(__DIR__ . "/DatabaseController.php");

class LocationController extends DatabaseController
{
    public function getLocationInfo($lat, $long, $timestamp, $portal): Location
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $location = $this->getLocation($lat, $long);
        $capitalCity = $this->getCapitalCity($location->countryName);
        $this->saveLocation($ipAddress, $lat, $long, $portal, date("Y-m-d", intval($timestamp)), date("H:i:s", intval($timestamp)), $location->countryName, $location->countryCode, $location->toponymName);
        return new Location($ipAddress, $long, $lat, $location->toponymName, $location->countryName, $capitalCity);
    }

    private function getLocation($lat, $long)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, "http://api.geonames.org/extendedFindNearbyJSON?lat=$lat&lng=$long&username=tppope");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.125 Safari/533.4");
        $str = curl_exec($curl);
        curl_close($curl);
        $location = json_decode($str);
        return $location->geonames[sizeof($location->geonames) - 2];
    }

    private function getCapitalCity($country)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, "https://restcountries.eu/rest/v2/name/$country");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.125 Safari/533.4");
        $str = curl_exec($curl);
        curl_close($curl);
        return json_decode($str)[0]->capital;
    }

    private function saveLocation($ipAddress, $lat, $long, $portal, $date, $time, $country, $countryCode, $place)
    {
        $statement = $this->mysqlDatabase->prepareStatement("INSERT INTO record (portal, latitude, longitude, ip_address, place_id, date, time)
                                                                    VALUES (:portal, :latitude, :longitude, :ipAddress, :placeId, :date, :time)");
        $statement->bindValue(':portal', $portal, PDO::PARAM_STR);
        $statement->bindValue(':ipAddress', $ipAddress, PDO::PARAM_STR);
        $statement->bindValue(':placeId', $this->getPlaceId($place, $country, $countryCode), PDO::PARAM_INT);
        $statement->bindValue(':date', $date, PDO::PARAM_STR);
        $statement->bindValue(':time', $time, PDO::PARAM_STR);
        $statement->bindValue(':latitude', $lat, PDO::PARAM_STR);
        $statement->bindValue(':longitude', $long, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $this->mysqlDatabase->getConnection()->lastInsertId();
        } catch (PDOException $PDOException) {
            throw $PDOException;
        }
    }

    private function getPlaceId($place, $country, $countryCode): int
    {
        $statement = $this->mysqlDatabase->prepareStatement("SELECT place.id FROM place WHERE place.toponym = :place");
        $statement->bindValue(':place', $place, PDO::PARAM_STR);
        $statement->execute();
        $placeId = $statement->fetchColumn();
        if ($placeId == null)
            $placeId = $this->insertPlace($place, $country, $countryCode);
        return $placeId;
    }

    private function insertPlace($place, $country, $countryCode): int
    {
        $statement = $this->mysqlDatabase->prepareStatement("INSERT INTO place (toponym, country_id)
                                                                    VALUES (:place, :countryId)");
        $statement->bindValue(':place', $place, PDO::PARAM_STR);
        $statement->bindValue(':countryId', $this->getCountryId($country, $countryCode), PDO::PARAM_INT);

        try {
            $statement->execute();
            return $this->mysqlDatabase->getConnection()->lastInsertId();
        } catch (PDOException $PDOException) {
            throw $PDOException;
        }
    }

    private function getCountryId($country, $countryCode): int
    {
        $statement = $this->mysqlDatabase->prepareStatement("SELECT country.id FROM country WHERE country.name = :country");
        $statement->bindValue(':country', $country, PDO::PARAM_STR);
        $statement->execute();
        $countryId = $statement->fetchColumn();
        if ($countryId == null)
            $countryId = $this->insertCountry($country, $countryCode);
        return $countryId;
    }

    private function insertCountry($country, $countryCode): int
    {
        $statement = $this->mysqlDatabase->prepareStatement("INSERT INTO country (name, code)
                                                                    VALUES (:country, :countryCode)");
        $statement->bindValue(':country', $country, PDO::PARAM_STR);
        $statement->bindValue(':countryCode', $countryCode, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $this->mysqlDatabase->getConnection()->lastInsertId();
        } catch (PDOException $PDOException) {
            throw $PDOException;
        }
    }
}
