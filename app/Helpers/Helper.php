<?php

use App\Models\Page;
use App\Models\Room;
use App\Models\Setting;

function getPage(){
    return  Page::where('id',1)->first();
}

function getRoom(){
    return Room::get();
}

function getSetting(){
    return Setting::where('id',1)->first();
}