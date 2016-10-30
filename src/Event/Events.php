<?php
/**
 * User: boshurik
 * Date: 28.10.16
 * Time: 17:06
 */

namespace BoShurik\IPGeoBase\Event;

final class Events
{
    const BEFORE_PARSE = 'parse.before';
    const BEFORE_CITIES = 'city.before';
    const CITY = 'city';
    const AFTER_CITIES = 'city.after';
    const BEFORE_RANGE = 'range.before';
    const RANGE = 'range';
    const AFTER_RANGE = 'range.after';
    const AFTER_PARSE = 'parse.after';
}