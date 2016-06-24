<?

$sn_mode_inf = array();

$sn_mode_inf["style-season"] = 'default';

$sn_mode_inf["bgcolor"] = "FFFAF0";
$sn_mode_inf["textcolor"] = "000000";
$sn_mode_inf["linkcolor"] = "000088";
$sn_mode_inf["vlinkcolor"] = "660066";
$sn_mode_inf["alinkcolor"] = "888800";
$sn_mode_inf["red_color"] = "AA0000";
$sn_mode_inf["offred_color"] = "770000";
$sn_mode_inf["bgimage_on"] = false;
$sn_mode_inf["bgimage_at"] = "";
// Note to anyone who develops a seasonal style-scheme --
// make sure that the background color is such that all the
// other colors are *clearly* visible against it.
//
// Also, if you use a background image, make sure of the same.
// Of course, if the image you really feel called to use does
// not fit that criterion, a decent knowledge of how to use
// image-editing progrmas (such as the Gimp) will allow you
// to make an altered form of the image that *does* fit that
// criterion. This can be done with the following five steps:
//
// 1> Import your source image (I'd say use a copy, not the
// original) into your image-editing program. With it, create
// a multi-layer project, which uses your source image as the
// background layer.
//
// 2> As a layer above the background, create a layer that is
// completely filled by a single solid color. Pick whichever
// color affords all the page's text the most visibility against
// it.
//
// 3> Adjust the transparency-level of the solid-color
// foreground layer so that your source image is clearly
// visible, but all text is clearly visible against the
// image as a whole.
//
// 4> Save your work.
//
// 5> Export your new, altered image to a web-firendly format.
//
// I should also note that the PHP script doesn't send the image
// itself to the browser - just a URL that references. The browser
// then has the task of fetching the image - and in doing so
// will treat it (if it's a relative link) relative to the root
// PHP page that it sees. Keep that in mind, too, if you dare
// to use a background image.

require_once ( $libdir . "/functions.php" );

?>