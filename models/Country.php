<?php


class Country implements \JsonSerializable
{
    private int $id;
    private string $name;
    private string $code;
    private array $places;
    private int $userCount = 0;

    public function countUserCount()
    {
        foreach ($this->places as $place)
            $this->userCount += $place->getUserCount();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return array
     */
    public function getPlaces(): array
    {
        return $this->places;
    }

    /**
     * @param array $places
     */
    public function setPlaces(array $places): void
    {
        $this->places = $places;
    }

    /**
     * @return int
     */
    public function getUserCount(): int
    {
        return $this->userCount;
    }

    /**
     * @param int $userCount
     */
    public function setUserCount(int $userCount): void
    {
        $this->userCount = $userCount;
    }


    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
