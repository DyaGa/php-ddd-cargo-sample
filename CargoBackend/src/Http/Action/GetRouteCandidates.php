<?php
/*
 * This file is part of the prooph/cargo-sample2.
 * (c) Alexander Miertsch <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 06.12.2015 - 11:23 PM
 */
namespace Codeliner\CargoBackend\Http\Action;

use Codeliner\CargoBackend\Application\Booking\BookingService;
use Codeliner\CargoBackend\Application\Booking\Dto\RouteCandidateDto;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class GetRouteCandidates
 *
 * @package Codeliner\CargoBackend\Application\Action
 */
final class GetRouteCandidates implements RequestHandlerInterface
{
    /**
     * @var BookingService
     */
    private $bookingService;

    /**
     * GetRouteCandidates constructor.
     *
     * @param BookingService $bookingService
     */
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (null === $trackingId = $request->getAttribute('trackingId')) {
            return new EmptyResponse(404);
        }

        $candidateIndex = 1;
        return new JsonResponse([
            'routeCandidates' => array_map(function(RouteCandidateDto $routeCandidate) use (&$candidateIndex) {
                $candidateData = $routeCandidate->getArrayCopy();
                $candidateData['id'] = $candidateIndex;
                $candidateIndex++;
                return $candidateData;
            }, $this->bookingService->requestPossibleRoutesForCargo($trackingId))
        ]);
    }
}
