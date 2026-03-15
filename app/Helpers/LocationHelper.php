<?
namespace App\Helpers;

class LocationHelper
{
  public static function isWithinRange($lat1, $lon1, $lat2, $lon2, $km = 1)
  {
    dd('hello');
    
    if (!$lat2 || !$lon2) return false;

    $earthRadius = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat / 2) * sin($dLat / 2) +
      cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
      sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c;

    return $distance <= $km;
  }
}