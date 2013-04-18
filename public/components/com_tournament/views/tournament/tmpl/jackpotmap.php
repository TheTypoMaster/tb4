<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>
<style type="text/css">


/***************************************************************/

div, p, a, img, table, tr, td, h1, h2, h3, form, br, ul, li{
  padding:0;
  margin:0;
  border-collapse:collapse;
  border:0;
  outline: none;
  }

.clear{
  clear:both;
  }

/***************************************************************/

.map-head{
  display:block;
  width:100%;
  text-align:center;
  padding:10px 0;
  }

.map-arrow{
  display:block;
  margin:5px auto;
  width:19px;
  height:19px;
  }

.map-tourn{
  width:498px;
  height:97px;
  background:#0386ee url(/components/com_tournament/images/jackpotmap/map-tourn.gif) no-repeat top left;
  display:block;
  margin:3px auto;
  color:#fff;
  font-weight:bold;
  position:relative;
  }

.map-jack{
  width:498px;
  height:97px;
  background:#0386ee url(/components/com_tournament/images/jackpotmap/map-jack.gif) no-repeat top left;
  display:block;
  margin:3px auto;
  color:#fff;
  font-weight:bold;
  position:relative;
  }

.map-title{
  font-weight:bold;
  font-size:16px;
  text-align:center;
  width:100%;
  line-height:1.7em;
  display:block;
  }

.map-entry{
  display:block;
  width:93px;
  height:59px;
  color:#222;
  font-size:12px;
  text-align:center;
  position:absolute;
  top:31px;
  left:17px;
  }

.map-info{
  display:block;
  width:376px;
  height:55px;
  color:#fff;
  font-size:18px;
  text-align:center;
  position:absolute;
  top:30px;
  left:126px;
  }

.map-info table{}

.map-info table tr{}

.map-info table tr td{
  padding:2px 5px 0 5px;
  }

td.map-info-data{
  padding:0 0 0 0;
  font-size:20px;
  }

td.map-info-entrants, td.map-info-prize{
  font-weight:normal;
  }

span.jackpot-map-title {
  color: #555555;
  display: block;
  font-size: 24px;
  font-weight: bold;
  margin: 0 auto;
  padding: 10px;
  text-align: center;
  text-transform: uppercase;
  width: 90%;
  }
</style>
<div class="modal-box">
  <span class="jackpot-map-title">Jackpot Map</span>
<?php
      foreach($this->jackpot_map as $tournament) {
        $container_class = ($tournament->parent_tournament_id > 0) ? 'map-tourn' : 'map-jack';
?>

    <div class="<?php print $container_class; ?>">
      <div class="map-title"><?php print $tournament->name; ?></div>
        <div class="map-entry"><?php print $tournament->display_value; ?><br/>Entry<br/>Ticket</div>
        <div class="map-info">
          <table>
              <tr>
                  <td class="map-info-entrants" align="right">Entrants: </td><td class="map-info-data" align="left"><?php print $tournament->entrants; ?></td>
				  <td class="map-info-prize" align="right">Prize Pool:  </td><td class="map-info-data" align="left"><?php print $tournament->prize_pool; ?></td>
                </tr>
                <tr>
                  <td class="map-info-prize" align="left">Date:  </td>
                  <td class="map-info-data" align="left" colspan="3"><?php print $tournament->start_date_display; ?></td>
                </tr>
            </table>
        </div>
    </div>
<?php
        if($tournament->parent_tournament_id > 0) {
?>
    <div class="map-arrow"><img src="components/com_tournament/images/jackpotmap/map-down-arrow.gif" alt="&darr;"/></div>
<?php
        }
      }
 ?>
</div>