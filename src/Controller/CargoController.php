<?php

namespace App\Controller;

use App\Entity\Cargo;
use App\Entity\CargoItem;
use App\Form\CargoType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as FOSRest;
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
    public function create(Request $request)
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
            return $this->handleView($this->view($return, 201));
        }

        return [
            'message' => 'Some error',
            'error' => $form->getErrors(),
        ];
    }

    /**
     * @Route("/cargo", name="cargo_list", methods={"GET"})
     * @FOSRest\View(serializerGroups={
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
     */
    public function list()
    {
        return $this->getDoctrine()->getRepository(Cargo::class)->findAll();

    }

    /**
     * @Route("/cargo/findUnique", name="cargo_unique", methods={"GET"})
     * @FOSRest\View()
     * @SWG\Response(
     *     response=200,
     *     description="Return the cargo ids of items without photos",
     *     @SWG\Schema(
     *          type="array",
     *          @SWG\Items(type= "string")
     *     )
     * )
     */
    public function findUniqueCargo(): array
    {
        $cargoRep = $this->getDoctrine()->getRepository(Cargo::class);
        $itemRep = $this->getDoctrine()->getRepository(CargoItem::class);

        $allUniqueItem = $itemRep->findItem();
        $noUniqueItemPictures = $itemRep->noPictures($allUniqueItem);

        $cargoIds = [];

        $exitIteracionValue = 1000; // Кол-во итераций в рекурсивном цикле до принудительного выхода
        $exitValue = 1;
        while (count($noUniqueItemPictures) !== 0) {
            $cargoId = $itemRep->getCargoByMaxWeightOfUniqueItems($noUniqueItemPictures);
            $exitValue++;
            $cargo = $cargoRep->find($cargoId);
            $cargoIds[] = $cargoId;
            $cargoItems = $cargo->getItem();
            /** @var CargoItem $item */
            foreach ($cargoItems as $item) {
                $key = array_search($item->getTitle(), $noUniqueItemPictures);
                if (false !== $key) {
                    unset($noUniqueItemPictures[$key]);
                }
            }
            if ($exitValue > $exitIteracionValue) {
                break;
            }
        }
        return $cargoIds;
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
     * @return Cargo
     */
    public function detail(int $id): Cargo
    {
        $cargo = $this->getDoctrine()
            ->getRepository(Cargo::class)
            ->find($id);
        if (!$cargo) {
            throw new NotFoundHttpException();
        }
        return $cargo;
    }


}
