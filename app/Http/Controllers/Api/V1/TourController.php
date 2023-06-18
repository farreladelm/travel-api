<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Travel;
use App\Http\Controllers\Controller;
use App\Http\Requests\ToursListRequest;
use App\Http\Resources\TourResource;

class TourController extends Controller
{
    public function index(Travel $travel, ToursListRequest $request)
    {
        // Users can filter (search) the results by priceFrom, priceTo, dateFrom (from that startingDate) and dateTo (until that
        // startingDate). User can sort the list by price asc and desc. They will always be sorted, after every additional
        // user-provided filter, by startingDate asc.

        $tours = $travel->tours()
            ->when($request->priceFrom, function ($query) use ($request) {
                $query->where('price', '>=', $request->priceFrom * 100);
            })
            ->when($request->priceTo, function ($query) use ($request) {
                $query->where('price', '<=', $request->priceTo * 100);
            })
            ->when($request->dateFrom, function ($query) use ($request) {
                $query->where('starting_date', '>=', $request->dateFrom);
            })
            ->when($request->dateTo, function ($query) use ($request) {
                $query->where('starting_date', '<=', $request->dateTo);
            })
            ->when($request->sortBy &&  $request->sortOrder, function ($query) use ($request) {
                $query->orderBy($request->sortBy, $request->sortOrder);
            })
            ->orderBy('starting_date')
            ->paginate();

        return TourResource::collection($tours);
    }
}
