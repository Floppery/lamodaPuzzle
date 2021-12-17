<?php

namespace App\DataFixtures;

use App\Entity\Cargo;
use App\Entity\CargoItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class AppFixtures extends Fixture
{
    protected int $cargoNumber = 1000;
    protected int $itemInOneCargo = 10;
    protected int $uniqueItem = 100;

    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        // Вычисляем кол-во уникальных товаров
        $uniqueItemAll = random_int($this->uniqueItem, $this->cargoNumber * $this->itemInOneCargo);
        $uniqueItemAllLeft = $uniqueItemAll - $this->uniqueItem;
        $uniqueArray = [];
        for ($i = 0; $i < $this->uniqueItem; $i++) {
            $uniqueValue = random_int(1, max($uniqueItemAllLeft, 1));
            $uniqueArray[$i] = $uniqueValue;
            $uniqueItemAllLeft -= $uniqueValue;
        }

        $output = new ConsoleOutput();
        $progressBar = new ProgressBar($output, $this->cargoNumber * $this->itemInOneCargo);
        $progressBar->start();

        // Заполняем базу
        for ($cargoId = 0; $cargoId < $this->cargoNumber; $cargoId++) {
            $cargo = new Cargo();
            $cargo->setTitle('Cargo #' . ($cargoId + 1));
            $manager->persist($cargo);

            $cargoArray = $this->GenerateCargo($uniqueArray);
            foreach ($cargoArray as $value) {
                for ($i = 0; $i < $value['value']; $i++) {
                    $item = (new CargoItem())
                        ->setTitle($value['id'] + 1)
                        ->setCargo($cargo);
                    $manager->persist($item);
                }
            }

            $progressBar->advance($this->itemInOneCargo);
            $manager->flush();
            $manager->clear();
        }
        $progressBar->finish();
        $output->writeln(' OK');
    }

    /**
     * @param array $uniqueArray
     * @return array
     * @throws Exception
     */
    private function GenerateCargo(array &$uniqueArray): array
    {
        $isUniqueLeft = (array_sum($uniqueArray) > 1);
        $cargo = [];
        $cargoItemRandomLeft = $this->itemInOneCargo;
        $cargoItems = 0;
        if ($isUniqueLeft) {
            $uniqueItemInCargo = random_int(1, $this->itemInOneCargo);
            $uniqueItemId = array_rand(array_keys($uniqueArray), $uniqueItemInCargo);
            $uniqueItemId = is_array($uniqueItemId) ? $uniqueItemId : [$uniqueItemId];
            $cargoItemRandomLeft -= count($uniqueItemId) - 1;

            foreach ($uniqueItemId as $key) {
                $itemValue = 1;
                if (1 !== $uniqueArray[$key]) {
                    $maxValue = min($uniqueArray[$key], $cargoItemRandomLeft);
                    $itemValue = ($maxValue > 1) ? random_int(1, $maxValue) : 1;
                    $cargoItemRandomLeft -= $itemValue;
                }
                $uniqueArray[$key] -= $itemValue;
                $cargoItems += $itemValue;
                $cargo[] = [
                    'id' => $key,
                    'value' => $itemValue
                ];
            }
        }
        if ($this->itemInOneCargo > $cargoItems) {
            // Добавляем не уникальными значениями id > 100
            $cargo[] = [
                'id' => random_int($this->uniqueItem + 1, $this->itemInOneCargo * $this->cargoNumber),
                'value' => $this->itemInOneCargo - $cargoItems
            ];
        }
        return $cargo;
    }
}
