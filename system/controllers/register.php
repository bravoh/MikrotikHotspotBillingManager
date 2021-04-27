<?php
/**
* PHP Mikrotik Billing (https://ibnux.github.io/phpmixbill/)
* @copyright	Copyright (C) 2014-2015 PHP Mikrotik Billing
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*
* created by iBNuX
**/

if (isset($routes['1'])) {
    $do = $routes['1'];
} else {
    $do = 'register-display';
}
use PEAR2\Net\RouterOS;
require_once 'system/autoload/PEAR2/Autoload.php';

switch($do){
    case 'post':
		
		$username = _post('username');
        $fullname = _post('fullname');
        $password = _post('password');
        $cpassword = _post('cpassword');
        $address = _post('address');
		$phonenumber = _post('phonenumber');
		$code = _post('kodevoucher');
		$v1 = ORM::for_table('tbl_voucher')->where('code',$code)->where('status',0)->find_one();
		if ($v1){
			$msg = '';
			/*
			if(Validator::Length($fullname,9,0) == false){
				$msg .= 'Nama harus antara 1 s/d 8 karakter'. '<br>';
			}
			

			$d = ORM::for_table('tbl_customers')->where('fullname',$fullname)->find_one();
			if($d){
				$msg .= $_L['account_already_exist']. '<br>';
			}
			*/
			if($msg == ''){
				$d = ORM::for_table('tbl_customers')->create();
				$d->username = $code;
				$d->password = $code;
				$d->fullname = $fullname;
				$d->address = $address;
				$d->phonenumber = $phonenumber;
				if($d->save()){
					$user = $d->id();
					//check voucher plan
					$p = ORM::for_table('tbl_plans')->find_one($v1['id_plan']);
					$c = ORM::for_table('tbl_customers')->find_one($user);
					$p = ORM::for_table('tbl_plans')->find_one($v1['id_plan']);
					
					$date_now = date("Y-m-d H:i:s");
					$date_only = date("Y-m-d");
					$time = date("H:i:s");
					
					$mikrotik = Router::_info($v1['routers']);
					$date_exp = date("Y-m-d", mktime(0,0,0,date("m"),date("d") + $p['validity'],date("Y")));

					if($v1['type'] == 'Hotspot'){
						try {
							$client = new RouterOS\Client($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
						} catch (Exception $e) {
							die('Unable to connect to the router.');
						}
						/* iBNuX Added:
						* 	Time limit to Mikrotik
						*	'Time_Limit', 'Data_Limit', 'Both_Limit'
						*/
						$addRequest = new RouterOS\Request('/ip/hotspot/user/add');
						if($p['typebp']=="Limited"){
							if($p['limit_type']=="Time_Limit"){
								if($p['time_unit']=='Hrs')
									$timelimit = $p['time_limit'].":00:00";
								else
									$timelimit = "00:".$p['time_limit'].":00";
								$client->sendSync($addRequest
									->setArgument('name', $v1['code'])
									->setArgument('profile', $p['name_plan'])
									->setArgument('password', $v1['code'])
									->setArgument('limit-uptime', $timelimit)
									->setArgument('comment', $v1['code'])
								);
							}else if($p['limit_type']=="Data_Limit"){
								if($p['data_unit']=='GB')
									$datalimit = $p['data_limit']."000000000";
								else
									$datalimit = $p['data_limit']."000000";
								$client->sendSync($addRequest
									->setArgument('name', $v1['code'])
									->setArgument('profile', $p['name_plan'])
									->setArgument('password', $v1['code'])
//									->setArgument('limit-uptime', $timelimit)
                                    ->setArgument('limit-bytes-total', $datalimit)
									->setArgument('comment', $v1['code'])
								);
							}else if($p['limit_type']=="Both_Limit"){
								if($p['time_unit']=='Hrs')
									$timelimit = $p['time_limit'].":00:00";
								else
									$timelimit = "00:".$p['time_limit'].":00";
								if($p['data_unit']=='GB')
									$datalimit = $p['data_limit']."000000000";
								else
									$datalimit = $p['data_limit']."000000";
								$client->sendSync($addRequest
									->setArgument('name', $v1['code'])
									->setArgument('profile', $p['name_plan'])
									->setArgument('password', $v1['code'])
									->setArgument('limit-uptime', $timelimit)
									->setArgument('limit-bytes-total', $datalimit)
									->setArgument('comment', $v1['code'])
								);
							}
						}else{
							$client->sendSync($addRequest
									->setArgument('name', $v1['code'])
									->setArgument('profile', $p['name_plan'])
									->setArgument('password', $v1['code'])
								->setArgument('comment', $v1['code'])
							);
						}
						
						$d = ORM::for_table('tbl_user_recharges')->create();
						$d->customer_id = $user;
						$d->username = $c['username'];
						$d->plan_id = $v1['id_plan'];
						$d->namebp = $p['name_plan'];
						$d->recharged_on = $date_only;
						$d->expiration = $date_exp;
						$d->time = $time;
						$d->status = "on";
						$d->method = "voucher";
						$d->routers = $v1['routers'];
						$d->type = "Hotspot";
						$d->save();
						
						// insert table transactions
						$t = ORM::for_table('tbl_transactions')->create();
						$t->invoice = "INV-"._raid(5);
						$t->username = $c['username'];
						$t->plan_name = $p['name_plan'];
						$t->price = $p['price'];
						$t->recharged_on = $date_only;
						$t->expiration = $date_exp;
						$t->time = $time;
						$t->method = "voucher";
						$t->routers = $v1['routers'];
						$t->type = "Hotspot";
						$t->save();
						
						$v1->status = "1";
						$v1->user = $c['username'];
						$v1->save();
						
					}else{
						try {
							$client = new RouterOS\Client($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
						} catch (Exception $e) {
							die('Unable to connect to the router.');
						}
						$addRequest = new RouterOS\Request('/ppp/secret/add');
						$client->sendSync($addRequest
							->setArgument('name', $c['username'])
							->setArgument('service', 'pppoe')
							->setArgument('profile', $p['name_plan'])
							->setArgument('password', $c['password'])
							->setArgument('comment', $c['username'])
						);
						
						$d = ORM::for_table('tbl_user_recharges')->create();
						$d->customer_id = $user;
						$d->username = $c['username'];
						$d->plan_id = $v1['id_plan'];
						$d->namebp = $p['name_plan'];
						$d->recharged_on = $date_only;
						$d->expiration = $date_exp;
						$d->time = $time;
						$d->status = "on";
						$d->method = "voucher";
						$d->routers = $v1['routers'];
						$d->type = "PPPOE";
						$d->save();
						
						// insert table transactions
						$t = ORM::for_table('tbl_transactions')->create();
						$t->invoice = "INV-"._raid(5);
						$t->username = $c['username'];
						$t->plan_name = $p['name_plan'];
						$t->price = $p['price'];
						$t->recharged_on = $date_only;
						$t->expiration = $date_exp;
						$t->time = $time;
						$t->method = "voucher";
						$t->routers = $v1['routers'];
						$t->type = "PPPOE";
						$t->save();

						$v1->status = "1";
						$v1->user = $c['username'];
						$v1->save();
					}
					
					$hs = ORM::for_table('tbl_routers')->where('name',$v1['routers'])->find_one();
					header("Location: http://".$hs['description']."/login?username=".$code."&password=".$code);

				}else{
					$ui->assign('username', $username);
					$ui->assign('fullname', $fullname);
					$ui->assign('address', $address);
					$ui->assign('phonenumber', $phonenumber);
					$ui->assign('notify','<div class="alert alert-danger">
					<button type="button" class="close" data-dismiss="alert">
					<span aria-hidden="true">×</span>
					</button>
					<div>Failed to register</div></div>');
					$ui->display('login.tpl');
				}
				//r2(U . 'register', 's', $_L['account_created_successfully']);
			}else{
				$ui->assign('username', $username);
				$ui->assign('fullname', $fullname);
				$ui->assign('address', $address);
				$ui->assign('phonenumber', $phonenumber);
				$ui->assign('notify','<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert">
				<span aria-hidden="true">×</span>
				</button>
				<div>'.$msg.'</div></div>');
				$ui->display('login.tpl');
			}
		}else{
			$ui->assign('username', $username);
			$ui->assign('fullname', $fullname);
			$ui->assign('address', $address);
			$ui->assign('phonenumber', $phonenumber);
			$ui->assign('notify','<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">
			<span aria-hidden="true">×</span>
			</button>
			<div>'.$_L['Voucher_Not_Valid'].'</div></div>');
			$ui->display('login.tpl');
			//r2(U . 'register', 'e', $_L['Voucher_Not_Valid']);
		}
				
        break;

    default:
		$ui->assign('username', "");
		$ui->assign('fullname', "");
		$ui->assign('address', "");
		$ui->assign('phonenumber', "");
        $ui->display('login.tpl');
        break;
}

