<?php
	header('Content-type: text/plain; charset=utf-8');
	include 'db/dbconnect.php';
	$filterstore = '';
	// $post_company = 'agc';
	$post_company = myPOST('company');

	// if($post_company == 'agc')
	// {
	// 	$filterstore = "AND (emp3.company_code = '01' AND emp3.bunit_code != '03')";
	// }
	// elseif ($post_company == 'asc') {
	// 	$filterstore = "AND (emp3.company_code = '02' AND emp3.bunit_code != '21')";
	// }
	// elseif ($post_company == 'mfi') {
	// 	$filterstore = "AND ((emp3.company_code = '03' AND emp3.bunit_code = '01') OR (emp3.company_code = '03' AND emp3.bunit_code = '32'))";
	// 	// $filterstore = "AND (emp3.company_code = '03') OR ((emp3.company_code = '03') AND (emp3.bunit_code != '03'))";
	// }
	// elseif ($post_company == 'mfi1') {
	// 	$filterstore = "AND ((emp3.company_code = '03' AND emp3.bunit_code = '01') OR (emp3.company_code = '03' AND emp3.bunit_code = '02') OR (emp3.company_code = '03' AND emp3.bunit_code = '04') OR (emp3.company_code = '03' AND emp3.bunit_code = '05') OR (emp3.company_code = '03' AND emp3.bunit_code = '06') OR (emp3.company_code = '03' AND emp3.bunit_code = '07') OR (emp3.company_code = '03' AND emp3.bunit_code = '08') OR (emp3.company_code = '03' AND emp3.bunit_code = '09') OR (emp3.company_code = '03' AND emp3.bunit_code = '10') OR (emp3.company_code = '03' AND emp3.bunit_code = '11') OR (emp3.company_code = '03' AND emp3.bunit_code = '12') OR (emp3.company_code = '03' AND emp3.bunit_code = '13') OR (emp3.company_code = '03' AND emp3.bunit_code = '14') OR (emp3.company_code = '03' AND emp3.bunit_code = '15') OR (emp3.company_code = '03' AND emp3.bunit_code = '16') OR (emp3.company_code = '03' AND emp3.bunit_code = '17') OR (emp3.company_code = '03' AND emp3.bunit_code = '18') OR (emp3.company_code = '03' AND emp3.bunit_code = '19') OR (emp3.company_code = '03' AND emp3.bunit_code = '20'))";
	// 	// $filterstore = "AND (emp3.company_code = '03') OR ((emp3.company_code = '03') AND (emp3.bunit_code != '03'))";
	// }
	// elseif ($post_company == 'mfi2') {
	// 	$filterstore = "AND ((emp3.company_code = '03' AND emp3.bunit_code = '21') OR (emp3.company_code = '03' AND emp3.bunit_code = '22') OR (emp3.company_code = '03' AND emp3.bunit_code = '23') OR (emp3.company_code = '03' AND emp3.bunit_code = '24') OR (emp3.company_code = '03' AND emp3.bunit_code = '255') OR (emp3.company_code = '03' AND emp3.bunit_code = '26') OR (emp3.company_code = '03' AND emp3.bunit_code = '27') OR (emp3.company_code = '03' AND emp3.bunit_code = '28') OR (emp3.company_code = '03' AND emp3.bunit_code = '29') OR (emp3.company_code = '03' AND emp3.bunit_code = '30') OR (emp3.company_code = '03' AND emp3.bunit_code = '31') OR (emp3.company_code = '03' AND emp3.bunit_code = '32') OR (emp3.company_code = '03' AND emp3.bunit_code = '33') OR (emp3.company_code = '03' AND emp3.bunit_code = '34') OR (emp3.company_code = '03' AND emp3.bunit_code = '35') OR (emp3.company_code = '03' AND emp3.bunit_code = '36') OR (emp3.company_code = '03' AND emp3.bunit_code = '37') OR (emp3.company_code = '03' AND emp3.bunit_code = '38') OR (emp3.company_code = '03' AND emp3.bunit_code = '39'))";
	// 	// $filterstore = "AND (emp3.company_code = '03') OR ((emp3.company_code = '03') AND (emp3.bunit_code != '03'))";
	// }
	// elseif ($post_company == 'ldi') {
	// 	$filterstore = "AND ((emp3.company_code = '04') OR (emp3.company_code = '06') OR (emp3.company_code = '08') OR (emp3.company_code = '09') OR (emp3.company_code = '10') OR (emp3.company_code = '11') OR (emp3.company_code = '12') OR (emp3.company_code = '13') OR (emp3.company_code = '14') OR (emp3.company_code = '15') OR (emp3.company_code = '16') OR (emp3.company_code = '17') OR (emp3.company_code = '18') OR (emp3.company_code = '19') OR (emp3.company_code = '20'))";
	// }
	// else{
	// 	$filterstore = "AND (emp3.company_code = '01')";
	// }

	if($post_company == 'agc')
	{
		$filterstore = "AND (emp3.company_code = '01' AND emp3.bunit_code != '03')";
	}
	elseif ($post_company == 'asc') {
		$filterstore = "AND (emp3.company_code = '02' AND emp3.bunit_code != '21')";
	}
	elseif ($post_company == 'mfi') {
		$filterstore = "AND ((emp3.company_code = '03' AND emp3.bunit_code = '01') OR (emp3.company_code = '03' AND emp3.bunit_code = '32'))";
		// $filterstore = "AND (emp3.company_code = '03') OR ((emp3.company_code = '03') AND (emp3.bunit_code != '03'))";
	}
	elseif ($post_company == 'mfi1') {
		$filterstore = "AND ((emp3.company_code = '03' AND emp3.bunit_code = '01') OR (emp3.company_code = '03' AND emp3.bunit_code = '02') OR (emp3.company_code = '03' AND emp3.bunit_code = '04') OR (emp3.company_code = '03' AND emp3.bunit_code = '05') OR (emp3.company_code = '03' AND emp3.bunit_code = '06') OR (emp3.company_code = '03' AND emp3.bunit_code = '07') OR (emp3.company_code = '03' AND emp3.bunit_code = '08') OR (emp3.company_code = '03' AND emp3.bunit_code = '09') OR (emp3.company_code = '03' AND emp3.bunit_code = '10') OR (emp3.company_code = '03' AND emp3.bunit_code = '11') OR (emp3.company_code = '03' AND emp3.bunit_code = '12') OR (emp3.company_code = '03' AND emp3.bunit_code = '13') OR (emp3.company_code = '03' AND emp3.bunit_code = '14') OR (emp3.company_code = '03' AND emp3.bunit_code = '15') OR (emp3.company_code = '03' AND emp3.bunit_code = '16') OR (emp3.company_code = '03' AND emp3.bunit_code = '17') OR (emp3.company_code = '03' AND emp3.bunit_code = '18') OR (emp3.company_code = '03' AND emp3.bunit_code = '19') OR (emp3.company_code = '03' AND emp3.bunit_code = '20'))";
		// $filterstore = "AND (emp3.company_code = '03') OR ((emp3.company_code = '03') AND (emp3.bunit_code != '03'))";
	}
	elseif ($post_company == 'mfi2') {
		$filterstore = "AND ((emp3.company_code = '03' AND emp3.bunit_code = '21') OR (emp3.company_code = '03' AND emp3.bunit_code = '22') OR (emp3.company_code = '03' AND emp3.bunit_code = '23') OR (emp3.company_code = '03' AND emp3.bunit_code = '24') OR (emp3.company_code = '03' AND emp3.bunit_code = '255') OR (emp3.company_code = '03' AND emp3.bunit_code = '26') OR (emp3.company_code = '03' AND emp3.bunit_code = '27') OR (emp3.company_code = '03' AND emp3.bunit_code = '28') OR (emp3.company_code = '03' AND emp3.bunit_code = '29') OR (emp3.company_code = '03' AND emp3.bunit_code = '30') OR (emp3.company_code = '03' AND emp3.bunit_code = '31') OR (emp3.company_code = '03' AND emp3.bunit_code = '32') OR (emp3.company_code = '03' AND emp3.bunit_code = '33') OR (emp3.company_code = '03' AND emp3.bunit_code = '34') OR (emp3.company_code = '03' AND emp3.bunit_code = '35') OR (emp3.company_code = '03' AND emp3.bunit_code = '36') OR (emp3.company_code = '03' AND emp3.bunit_code = '37') OR (emp3.company_code = '03' AND emp3.bunit_code = '38') OR (emp3.company_code = '03' AND emp3.bunit_code = '39'))";
		// $filterstore = "AND (emp3.company_code = '03') OR ((emp3.company_code = '03') AND (emp3.bunit_code != '03'))";
	}
	elseif ($post_company == 'others1') {
		$filterstore = "AND ((emp3.company_code = '04') OR (emp3.company_code = '06') OR (emp3.company_code = '08') OR (emp3.company_code = '09'))";
	}
	elseif ($post_company == 'others2') {
		$filterstore = "AND ((emp3.company_code = '10') OR (emp3.company_code = '11') OR (emp3.company_code = '12') OR (emp3.company_code = '13') OR (emp3.company_code = '14') OR (emp3.company_code = '15') OR (emp3.company_code = '16') OR (emp3.company_code = '17') OR (emp3.company_code = '18') OR (emp3.company_code = '19') OR (emp3.company_code = '20'))";
	}
	else{
		$filterstore = "AND (emp3.company_code = '01')";
	}

	$query = mysqlm_query("SELECT
								emp3.emp_no,
								emp3.emp_pins,
								emp3.name,
								COALESCE(crlim.cred_balance,'0.00') AS credit_limit,
								pis.locate_company.acroname,
								pis.locate_business_unit.business_unit,
								emp3.dept_code,
								IF((emp3.payroll_no='' AND emp3.emp_type LIKE '%NESCO%'),'NESCO',emp3.payroll_no) AS payroll_no,
								emp3.emp_id,
								IF((emp3.emp_type LIKE '%regular%'),'REGULAR',emp3.eocdate) AS eocdate,
								pis.locate_company.company_code,
								pis.locate_business_unit.bunit_code,
								dept.dept_name,
								emp3.company_code,
								emp3.bunit_code
							FROM  pis.employee3 emp3
							LEFT JOIN ebs.ec_employeebalance crlim
								ON emp3.emp_id = crlim.cred_empid
							LEFT JOIN
								pis.locate_company
								ON emp3.company_code = pis.locate_company.company_code
							LEFT JOIN
								pis.locate_business_unit
								ON emp3.bunit_code = pis.locate_business_unit.bunit_code
								AND emp3.company_code = pis.locate_business_unit.company_code
							LEFT JOIN pis.locate_department as dept
								ON dept.dcode = CONCAT(emp3.company_code, emp3.bunit_code, emp3.dept_code)
				            WHERE emp3.current_status='Active'
				            		AND UCASE(emp3.emp_type) NOT IN ('COMMISSION BASED','NICO','PROMO','PROMO-NESCO','CYDEM','NEMPEX','PW','PAKYAWAN','BACK-UP','OJT','SEASONAL','TAMBLOT')
				            		AND (emp3.payroll_no!='' OR emp3.emp_type LIKE '%NESCO%')
				            		AND (emp3.emp_no !='' AND emp3.emp_pins !='')
							".$filterstore);

	$arr1 = array();
	while($row=mysqlm_fetch_array($query)){

		$forledger = mysqlm_query("SELECT led_balance FROM ec_ledger WHERE led_empid = '".$row[8]."' ORDER BY led_ledgerid DESC LIMIT 1");
		$fetchledger = mysqlm_fetch_array($forledger);
		$empbalance = "0.00";
		if(count($fetchledger['led_balance'])>0){
			$empbalance = $fetchledger['led_balance'];
		}


		if(in_array($row[12], array('HOME & FASHION','HOME and FASHION','Home and Fashion Distribution Center','HARDWARE DISTRIBUTION CENTER','CEBU EXTENSION OFFICE','Manila Extension Office','FIXRITE','EASY FIX'))):
		else:
			$arr2 = array();
			$arr2[] = trim($row[0]);
			$arr2[] = trim($row[1]);
			$arr2[] = trim(iconv( mb_detect_encoding($row[2], "UTF-8,ISO-8859-1"), "UTF-8", $row[2]));
			$arr2[] = trim($row[3]);
			$arr2[] = trim($row[4]);
			$arr2[] = trim($row[5]);
			$arr2[] = trim($row[12]);
			$arr2[] = trim($row[7]);
			$arr2[] = trim($row[8]);
			$arr2[] = trim($row[9]);
			$arr2[] = trim($empbalance);
			array_push($arr1,$arr2);
		endif;
		
	}
	//echo json_encode($arr1);

	echo json_encode(utf8ize($arr1));
	function utf8ize($d) {
		if (is_array($d)) {
			foreach ($d as $k => $v) {
				$d[$k] = utf8ize($v);
			}
		} else if (is_string ($d)) {
			return utf8_encode($d);
		}
		return $d;
	}
	breakhere($con);
?>