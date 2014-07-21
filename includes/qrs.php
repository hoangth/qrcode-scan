<?php
require_once('classes/database.php');
require_once('classes/infos.php');
require_once('classes/ouvrier.php');

function checkIn($qrcode)
{
	$ou = new Ouvrier();
	if($ou->find_by_qrcode($qrcode)) {
		$ou_data = $ou->get_ouvrier();
		date_default_timezone_set('Africa/Casablanca');
		$heure = date("H:i:s");
		$jour = date("Y-m-d");
		$infos = Infos::tous_infos_ouvrier($ou_data['o_id'], 1);
		
		foreach ($infos as $info) {
			$in = new Infos();
			$b = strtotime($heure);
			if (!is_null($info['heure_fin'])) {
				$a = strtotime($info['heure_fin']);
				if($b - $a > 36000) {

					$info_data = array(
						"i_jour"=>$jour,
						"heure_debut"=>$heure,		
						"o_id"=>$ou_data['o_id']
					);
					foreach ($info_data as $key => $value) {
						$in->set_infos($key,$value);
					}
					$in->create();
				}
			} else {
				
				$a = strtotime($info['heure_fin']);
				if($b - $a > 36000) {
					
					$in->find_by_id($info['i_id']);
					$in->set_infos("heure_fin",$heure);
					$in->update();						
				}
			}
		}
		echo '<div class="alert alert-info">
	            <button type="button" class="close" data-dismiss="alert">&times;</button>
	            <strong>Warning!</strong> Best check yo self, yo not looking too good.
	        </div>';
	}
}