<?php

namespace OpenDominion\Http\Controllers\Dominion;

use OpenDominion\Calculators\Dominion\BuildingCalculator;
use OpenDominion\Calculators\Dominion\ImprovementCalculator;
use OpenDominion\Calculators\Dominion\LandCalculator;
use OpenDominion\Calculators\Dominion\MilitaryCalculator;
use OpenDominion\Calculators\Dominion\PopulationCalculator;
use OpenDominion\Calculators\Dominion\ProductionCalculator;
use OpenDominion\Calculators\Dominion\SpellCalculator;
use OpenDominion\Exceptions\GameException;
use OpenDominion\Helpers\BuildingHelper;
use OpenDominion\Helpers\ImprovementHelper;
use OpenDominion\Helpers\LandHelper;
use OpenDominion\Helpers\MiscHelper;
use OpenDominion\Helpers\RankingsHelper;
use OpenDominion\Helpers\ResourceHelper;
use OpenDominion\Helpers\SpellHelper;
use OpenDominion\Helpers\TechHelper;
use OpenDominion\Helpers\UnitHelper;
use OpenDominion\Mappers\Dominion\InfoMapper;
use OpenDominion\Models\Dominion;
use OpenDominion\Services\Dominion\QueueService;
use OpenDominion\Services\Dominion\RankingsService;

class AdvisorsController extends AbstractDominionController
{
    public function getAdvisors()
    {
        return redirect()->route('dominion.advisors.production');
    }

    public function getAdvisorsStatus(Dominion $target)
    {
        try {
            $this->guardPackRealm($target);
        } catch (GameException $e) {
            return redirect()->back()
                ->withErrors([$e->getMessage()]);
        }

        return view('pages.dominion.advisors.status', [
            'unitHelper' => app(UnitHelper::class),
            'miscHelper' => app(MiscHelper::class),
            'infoMapper' => app(InfoMapper::class),
            'targetDominion' => $target
        ]);
    }

    public function getAdvisorsProduction(Dominion $target = null)
    {
        try {
            $this->guardPackRealm($target);
        } catch (GameException $e) {
            return redirect()->back()
                ->withErrors([$e->getMessage()]);
        }

        return view('pages.dominion.advisors.production', [
            'populationCalculator' => app(PopulationCalculator::class),
            'productionCalculator' => app(ProductionCalculator::class),
            'infoMapper' => app(InfoMapper::class),
            'targetDominion' => $target
        ]);
    }

    public function getAdvisorsMilitary(Dominion $target = null)
    {
        try {
            $this->guardPackRealm($target);
        } catch (GameException $e) {
            return redirect()->back()
                ->withErrors([$e->getMessage()]);
        }

        return view('pages.dominion.advisors.military', [
            'queueService' => app(QueueService::class),
            'unitHelper' => app(UnitHelper::class),
            'infoMapper' => app(InfoMapper::class),
            'resourceHelper' => app(ResourceHelper::class),
            'targetDominion' => $target
        ]);
    }

    public function getAdvisorsLand(Dominion $target = null)
    {
        try {
            $this->guardPackRealm($target);
        } catch (GameException $e) {
            return redirect()->back()
                ->withErrors([$e->getMessage()]);
        }

        return view('pages.dominion.advisors.land', [
            'landCalculator' => app(LandCalculator::class),
            'landHelper' => app(LandHelper::class),
            'unitHelper' => app(UnitHelper::class),
            'queueService' => app(QueueService::class),
            'infoMapper' => app(InfoMapper::class),

            'targetDominion' => $target
        ]);
    }

    public function getAdvisorsConstruction(Dominion $target = null)
    {
        try {
            $this->guardPackRealm($target);
        } catch (GameException $e) {
            return redirect()->back()
                ->withErrors([$e->getMessage()]);
        }

        return view('pages.dominion.advisors.construction', [
            'buildingCalculator' => app(BuildingCalculator::class),
            'buildingHelper' => app(BuildingHelper::class),
            'landCalculator' => app(LandCalculator::class),
            'queueService' => app(QueueService::class),
            'infoMapper' => app(InfoMapper::class),
            'targetDominion' => $target
        ]);
    }

    public function getAdvisorsMagic(Dominion $target = null)
    {
        try {
            $this->guardPackRealm($target);
        } catch (GameException $e) {
            return redirect()->back()
                ->withErrors([$e->getMessage()]);
        }

        return view('pages.dominion.advisors.magic', [
            'spellCalculator' => app(SpellCalculator::class),
            'spellHelper' => app(SpellHelper::class),
            'infoMapper' => app(InfoMapper::class),
            'targetDominion' => $target
        ]);
    }

    public function getAdvisorsCastle(Dominion $target = null)
    {
        try {
            $this->guardPackRealm($target);
        } catch (GameException $e) {
            return redirect()->back()
                ->withErrors([$e->getMessage()]);
        }

        return view('pages.dominion.advisors.castle', [
            'improvementCalculator' => app(ImprovementCalculator::class),
            'improvementHelper' => app(ImprovementHelper::class),
            'techHelper' => app(TechHelper::class),
            'infoMapper' => app(InfoMapper::class),
            'targetDominion' => $target
        ]);
    }

    public function getAdvisorsRankings(Dominion $target = null)
    {
        try {
            $this->guardPackRealm($target);
        } catch (GameException $e) {
            return redirect()->back()
                ->withErrors([$e->getMessage()]);
        }

        return view('pages.dominion.advisors.rankings', [
            'rankingsHelper' => app(RankingsHelper::class),
            'rankingsService' => app(RankingsService::class),
            'targetDominion' => $target
        ]);
    }

    public function getAdvisorsStatistics(Dominion $target = null)
    {
        try {
            $this->guardPackRealm($target);
        } catch (GameException $e) {
            return redirect()->back()
                ->withErrors([$e->getMessage()]);
        }

        return view('pages.dominion.advisors.statistics', [
            'landCalculator' => app(LandCalculator::class),
            'militaryCalculator' => app(MilitaryCalculator::class),
            'populationCalculator' => app(PopulationCalculator::class),
            'targetDominion' => $target
        ]);
    }

    private function guardPackRealm(?Dominion $target)
    {
        if($target == null) {
            return;
        }

        $dominion = $this->getSelectedDominion();

        if ($dominion->locked_at !== null) {
            throw new GameException('Locked dominions are not allowed to look at realm advisors.');
        }

        if($dominion->realm_id !== $target->realm_id)
        {
            throw new GameException('You are only allowed to look at dominions in your realm.');
        }

        $realmAdvisors = $target->getSetting('realmadvisors');

        // Realm Advisor is explicitly enabled
        if ($realmAdvisors && array_key_exists($dominion->id, $realmAdvisors) && $realmAdvisors[$dominion->id] === true) {
            return;
        }

        // Realm Advisor is explicity disabled
        if ($realmAdvisors && array_key_exists($dominion->id, $realmAdvisors) && $realmAdvisors[$dominion->id] === false) {
            throw new GameException('This user has opted not to share their advisors.');
        }

        // Pack Advisor is enabled
        if ($target->user != null && $target->user->getSetting('packadvisors') !== false && ($dominion->pack_id != null && $dominion->pack_id == $target->pack_id)) {
            return;
        }

        throw new GameException('This user has opted not to share their advisors.');
    }
}
