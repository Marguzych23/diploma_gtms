<?php


namespace App\Service;


use App\Repository\IndustryRepository;

class IndustryService
{
    private IndustryRepository $industryRepository;

    /**
     * IndustryService constructor.
     *
     * @param IndustryRepository $industryRepository
     */
    public function __construct(
        IndustryRepository $industryRepository
    ) {
        $this->industryRepository = $industryRepository;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->industryRepository->findAll();
    }
}