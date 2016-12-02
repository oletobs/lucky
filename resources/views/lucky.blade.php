<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>u lucky? @yield('title')</title>

    @section('stylesheets')
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @show

    <script src="{{ asset('js/sortable.js') }}"></script>
</head>

<body>

@section('header')

@show

@section('content')
    <table class="table table-inverse sortable">
        <thead>
        <tr>
            <!--<th>Guild Rank</th>-->
            <th>Name</th>
            <th>Class</th>
            <th>Spec</th>
            <th class="left-border">EoA</th>
            <th>DHT</th>
            <th>NL</th>
            <th>HoV</th>
            <th>VotW</th>
            <th>BRH</th>
            <th>MoS</th>
            <th>Arcway</th>
            <th>CoS</th>
            <th>Karazhan</th>
            <th>Total</th>
            <th class="left-border">LFR</th>
            <th>Normal</th>
            <th>Heroic</th>
            <th>Mythic</th>
            <th>Total</th>
            <th class="left-border">World Quests</th>
            <th class="left-border">Equipped ilvl</th>
            <th>Average ilvl</th>
            <th>Artifact ilvl</th>
            <th class="left-border">Total Artifact Power</th>
            <th>Highest Artifact lvl</th>
        </tr>
        </thead>
        <tbody>
        @foreach($members as $key=>$member)
            <tr>
                <!--<td>{{ $member->rank }}</td>-->
                <td>{{ $member->character->name }}</td>
                <td>{{ $class[$member->character->class] }}</td>
                    @if (isset($member->character->spec))
                        <td>{{ $member->character->spec->name }}</td>
                    @else
                        <td>No spec</td>
                    @endif
                <td class="left-border">{{ $member->statistics->subCategories[5]->subCategories[6]->statistics[2]->quantity }}</td>
                <td>{{ $member->statistics->subCategories[5]->subCategories[6]->statistics[5]->quantity }}</td>
                <td>{{ $member->statistics->subCategories[5]->subCategories[6]->statistics[8]->quantity }}</td>
                <td>{{ $member->statistics->subCategories[5]->subCategories[6]->statistics[11]->quantity }}</td>
                <td>{{ $member->statistics->subCategories[5]->subCategories[6]->statistics[20]->quantity }}</td>
                <td>{{ $member->statistics->subCategories[5]->subCategories[6]->statistics[23]->quantity }}</td>
                <td>{{ $member->statistics->subCategories[5]->subCategories[6]->statistics[26]->quantity }}</td>
                <td>{{ $member->statistics->subCategories[5]->subCategories[6]->statistics[27]->quantity }}</td>
                <td>{{ $member->statistics->subCategories[5]->subCategories[6]->statistics[28]->quantity }}</td>
                <td>{{ $member->statistics->subCategories[5]->subCategories[6]->statistics[29]->quantity }}</td>
                <td>{{ $member->totalMythicRuns }}</td>
                <td class="left-border">{{ $member->totalLFR }}</td>
                <td>{{ $member->totalNormal }}</td>
                <td>{{ $member->totalHeroic }}</td>
                <td>{{ $member->totalMythic }}</td>
                <td>{{ $member->totalRaid }}</td>
                <td class="left-border">{{ $member->totalWQ }}</td>
                <td class="left-border">{{ $member->items->averageItemLevelEquipped }}</td>
                <td>{{ $member->items->averageItemLevel }}</td>
                @if (isset($member->items->mainHand))
                    <td>{{ $member->items->mainHand->itemLevel }}</td>
                @elseif (isset($member->items->offHand))
                    <td>{{ $member->items->offHand->itemLevel }}</td>
                @else
                    <td>Artifact mia!</td>
                @endif
                <td class="left-border">{{ $member->totalAP }}</td>
                <td>{{ $member->maxArtLvl }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@show

@section('footer')

@show

@section('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
@show

</body>

</html>