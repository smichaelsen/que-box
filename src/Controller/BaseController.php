<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Card;
use App\Entity\Cycle;
use App\Repository\CardRepository;
use App\Repository\CycleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class BaseController extends Controller
{

    protected function getCardRepository(): CardRepository
    {
        /** @var CardRepository $cardRepository */
        $cardRepository = $this->getDoctrine()->getManager()->getRepository(Card::class);
        return $cardRepository;
    }

    protected function getCycleRepository(): CycleRepository
    {
        /** @var CycleRepository $cycleRepository */
        $cycleRepository = $this->getDoctrine()->getManager()->getRepository(Cycle::class);
        return $cycleRepository;
    }

}