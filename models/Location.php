<?php


class Location implements \JsonSerializable
{
    private string $ipAddress;
    private string $long;
    private string $lat;
    private string $city;
    private string $state;
    private string $capital;

    /**
     * Location constructor.
     * @param string $ipAddress
     * @param string $long
     * @param string $lat
     * @param string $city
     * @param string $state
     * @param string $capital
     */
    public function __construct(string $ipAddress, string $long, string $lat, string $city, string $state, string $capital)
    {
        $this->ipAddress = $ipAddress;
        $this->long = $long;
        $this->lat = $lat;
        $this->city = $city;
        $this->state = $state;
        $this->capital = $capital;
    }


    /**
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress(string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return string
     */
    public function getLong(): string
    {
        return $this->long;
    }

    /**
     * @param string $long
     */
    public function setLong(string $long): void
    {
        $this->long = $long;
    }

    /**
     * @return string
     */
    public function getLat(): string
    {
        return $this->lat;
    }

    /**
     * @param string $lat
     */
    public function setLat(string $lat): void
    {
        $this->lat = $lat;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getCapital(): string
    {
        return $this->capital;
    }

    /**
     * @param string $capital
     */
    public function setCapital(string $capital): void
    {
        $this->capital = $capital;
    }


    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
