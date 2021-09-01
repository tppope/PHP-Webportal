<?php
require_once (__DIR__."/DatabaseController.php");
require_once (__DIR__."/../models/Country.php");
require_once (__DIR__."/../models/Place.php");

class StatsController extends DatabaseController
{

    /**
     * @throws Exception
     */
    public function getUserAttendance(): array{
        $countries = $this->getCountries();
        foreach ($countries as $country) {
            $country->setPlaces($this->getPlaceAttendance($country->getId()));
            $country->countUserCount();
        }
        return $countries;
    }

    public function getGPS(){
        $statement = $this->mysqlDatabase->prepareStatement("SELECT DISTINCT latitude, longitude FROM record");

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_NUM);
        }
        catch (Exception $exception){
            throw $exception;
        }
    }

    public function getWebsiteAttendance(): array{
        $statement = $this->mysqlDatabase->prepareStatement("SELECT record.portal, COUNT(*) AS attendance FROM record GROUP BY record.portal ORDER BY attendance DESC");

        try {
            $statement->execute();
            return $statement->fetchAll();
        }
        catch (Exception $exception){
            throw $exception;
        }
    }

    public function getHourAttendance(): array{
        $statement1 = $this->mysqlDatabase->prepareStatement("SELECT COUNT(*) as attendance FROM record WHERE record.time BETWEEN '00:00:00' AND '05:59:59'");
        $statement2 = $this->mysqlDatabase->prepareStatement("SELECT COUNT(*) as attendance FROM record WHERE record.time BETWEEN '06:00:00' AND '14:59:59'");
        $statement3 = $this->mysqlDatabase->prepareStatement("SELECT COUNT(*) as attendance FROM record WHERE record.time BETWEEN '15:00:00' AND '20:59:59'");
        $statement4 = $this->mysqlDatabase->prepareStatement("SELECT COUNT(*) as attendance FROM record WHERE record.time BETWEEN '21:00:00' AND '23:59:59'");
        try {
            $statement1->execute();
            $statement2->execute();
            $statement3->execute();
            $statement4->execute();
            return array("hour00"=>$statement1->fetchColumn(),"hour06"=>$statement2->fetchColumn(),"hour15"=>$statement3->fetchColumn(),"hour21"=>$statement4->fetchColumn());
        }
        catch (Exception $exception){
            throw $exception;
        }
    }

    /**
     * @throws Exception
     */
    private function getCountries(): array{
        $statement = $this->mysqlDatabase->prepareStatement("SELECT country.id, country.name, country.code FROM country");

        try {
            $statement->setFetchMode(PDO::FETCH_CLASS, "Country");
            $statement->execute();
            return $statement->fetchAll();
        }
        catch (Exception $exception){
            throw $exception;
        }
    }

    /**
     * @throws Exception
     */
    private function getPlaceAttendance($countryId): array{
       $statement = $this->mysqlDatabase->prepareStatement("SELECT id, name, COUNT(*) AS userCount FROM(
                                                                    SELECT record.date, place.id AS id, place.toponym AS name, record.ip_address
                                                                    FROM record
                                                                    INNER JOIN place ON place.id = record.place_id
                                                                    WHERE country_id = :countryId
                                                                    GROUP BY  record.date, place.toponym, record.ip_address) AS ipCount
                                                                    GROUP BY name");

       try {
           $statement->bindValue(":countryId",$countryId,PDO::PARAM_INT);
           $statement->setFetchMode(PDO::FETCH_CLASS, "Place");
           $statement->execute();
           return $statement->fetchAll();
       }
       catch (Exception $exception){
           throw $exception;
       }
   }
}
