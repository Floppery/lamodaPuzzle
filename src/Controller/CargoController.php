<?php

namespace App\Controller;

use App\Entity\Cargo;
use App\Entity\CargoItem;
use App\Form\CargoType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @Route("/api")
 */
class CargoController extends AbstractFOSRestController
{
    /**
     * @Route("/cargo", name="cargo_create", methods={"POST"})
     * @FOSRest\View(serializerGroups={
     *     "cargo_details",
     *     "cargo_item_details"
     * })
     *
     * @SWG\Post(
     *     description="Create cargo with items",
     *     @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      default="{}",
     *      description="Cargo object with items in json format",
     *      @SWG\Schema(
     *          ref=@Model(type=Cargo::class, groups={"cargo_list"}))
     *      )
     *      ),
     *     @SWG\Response(
     *      response=201,
     *      description="Create successful",
     *      )
     * )
     *
     */
    public function create(Request $request): Response
    {
        $cargo = new Cargo();

        $form = $this->createForm(CargoType::class, $cargo);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            /** @var Cargo $cargo */
            $cargo = $form->getData();
            $cargoItems = $cargo->getItem();
            /** @var CargoItem $item */
            foreach ($cargoItems as $item) {
                $item->setCargo($cargo);
            }
            $this->getDoctrine()->getManager()->persist($cargo);
            $this->getDoctrine()->getManager()->flush();
            $return = [
                'message' => 'Create successful',
                'data' => $cargo
            ];
            $view = $this->view($return, 201);
        }else{
            $error = [
                'message' => 'Some error',
                'error' => $form->getErrors(),
            ];
            $view = $this->view($error, 400);
        }

        return $this->handleView($view);
    }

    /**
     * @Route("/cargo", name="cargo_list", methods={"GET"})
     * FOSRest\View(serializerGroups={
     *     "cargo_list"
     * })
     * @SWG\Response(
     *     response=200,
     *     description="Return list of all cargos",
     *     @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref=@Model(type=Cargo::class, groups={"cargo_list"}))
     *     )
     * )
     * @return Cargo[]
     */
    public function list(): array
    {
        return $this->getDoctrine()
            ->getRepository(Cargo::class)
            ->findAll();
    }

    /**
     * @Route("/cargo/findUnique", name="cargo_unique", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Return the cargo ids of items without photos",
     *     @SWG\Schema(
     *          type="array",
     *          @SWG\Items(type= "string")
     *     )
     * )
     */
    public function findUniqueCargo(): Response
    {
        $cargoRep = $this->getDoctrine()
            ->getRepository(Cargo::class);
        $itemRep = $this->getDoctrine()
            ->getRepository(CargoItem::class);

        $allUniqueItem = $itemRep->findItem();
        $noUniqueItemPictures = $itemRep->noPictures($allUniqueItem);

        $cargoIds = [];

        $exitIterationValue = 1000; // Кол-во итераций в рекурсивном цикле до принудительного выхода
        $exitValue = 0;
        while (count($noUniqueItemPictures) !== 0) {

            if ($exitValue >= $exitIterationValue) {
                break;
            }
            $exitValue++;

            $cargoId = $itemRep->getCargoByMaxWeightOfUniqueItems($noUniqueItemPictures);

            /** @var Cargo $cargo */
            $cargo = $cargoRep->find($cargoId);
            $cargoIds[] = $cargoId;
            $cargoItems = $cargo->getItem();

            /** @var CargoItem $item */
            foreach ($cargoItems as $item) {
                $key = array_search($item->getTitle(), $noUniqueItemPictures, true);
                if (false !== $key) {
                    unset($noUniqueItemPictures[$key]);
                }
            }
        }

        $view = $this->view($cargoIds, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/cargo/{id}", name="cargo_detail", methods={"GET"})
     * @FOSRest\View(serializerGroups={
     *     "cargo_details",
     *     "cargo_item_details"
     * })
     * @SWG\Response(
     *     response=200,
     *     description="Return cargo by id",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref=@Model(type=Cargo::class, groups={"cargo_details", "cargo_item_details"}))
     *     )
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     required= true,
     *     description="id cargo"
     * )
     * @param int $id
     * @return Response
     */
    public function detail(int $id): Response
    {
        $cargo = $this->getDoctrine()
            ->getRepository(Cargo::class)
            ->find($id);

        if (!$cargo) {
            throw new NotFoundHttpException('Cargo not found');
        }

        $view = $this->view($cargo, 200);
        return $this->handleView($view);
    }

}
