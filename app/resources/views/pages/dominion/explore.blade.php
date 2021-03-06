@extends('layouts.master')

@section('page-header', 'Explore')

@section('content')
    <div class="row">

        <div class="col-sm-12 col-md-9">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="ra ra-telescope"></i> Explore Land</h3>
                </div>
                <form action="{{ route('dominion.explore') }}" method="post" role="form">
                    @csrf
                    <div class="box-body table-responsive no-padding">
                        <table class="table">
                            <colgroup>
                                <col>
                                <col width="10%">
                                <col width="10%">
                                <col width="10%">
                                <col width="10%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>Terrain</th>
                                    <th class="text-center">Owned</th>
                                    <th class="text-center">Barren</th>
                                    <th class="text-center">Exploring</th>
                                    <th class="text-center">Explore For</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($landHelper->getLandTypes() as $landType)
                                    <tr>
                                        <td>
                                            {{ ucfirst($landType) }}
                                            @if ($landType === $selectedDominion->race->home_land_type)
                                                <br>
                                                <small class="text-muted"><i><span title="This is the land type where your {{ strtolower($selectedDominion->race->name) }} race constructs home buildings on">Home land type</span></i></small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($selectedDominion->{'land_' . $landType}) }}
                                            <small>
                                                ({{ number_format((($selectedDominion->{'land_' . $landType} / $landCalculator->getTotalLand($selectedDominion)) * 100), 1) }}%)
                                            </small>
                                        </td>
                                        <td class="text-center">{{ number_format($landCalculator->getTotalBarrenLandByLandType($selectedDominion, $landType)) }}</td>
                                        <td class="text-center">{{ number_format($queueService->getExplorationQueueTotalByResource($selectedDominion, "land_{$landType}")) }}</td>
                                        <td class="text-center">
                                            <input type="number" name="explore[land_{{ $landType }}]" class="form-control text-center" placeholder="0" min="0" max="{{ $explorationCalculator->getMaxAfford($selectedDominion) }}" value="{{ old('explore.' . $landType) }}" {{ $selectedDominion->isLocked() ? 'disabled' : null }}>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" {{ $selectedDominion->isLocked() || $selectedDominion->round->hasOffensiveActionsDisabled() ? 'disabled' : null }}>Explore</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-sm-12 col-md-3">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Information</h3>
                    <a href="{{ route('dominion.advisors.land') }}" class="pull-right">Land Advisor</a>
                </div>
                <div class="box-body">
                    <p>Exploration will net you additional acres of barren land to construct buildings upon and will take <b>12 hours</b> to process.</p>
                    <p>Exploration per acre of barren land will come at a cost of {{ number_format($explorationCalculator->getPlatinumCost($selectedDominion)) }} platinum and {{ number_format($explorationCalculator->getDrafteeCost($selectedDominion)) }} {{ str_plural('draftee', $explorationCalculator->getDrafteeCost($selectedDominion)) }}.</p>
                    <p>You have {{ number_format($selectedDominion->resource_platinum) }} platinum and {{ number_format($selectedDominion->military_draftees) }} {{ str_plural('draftee', $selectedDominion->military_draftees) }}.</p>
                    <p>You can afford to explore for <b>{{ number_format($explorationCalculator->getMaxAfford($selectedDominion)) }} {{ str_plural('acre', $explorationCalculator->getMaxAfford($selectedDominion)) }} of barren land</b> at that rate.</p>
                </div>
            </div>
        </div>

    </div>
@endsection
