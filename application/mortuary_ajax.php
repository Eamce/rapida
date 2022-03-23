<?php
	session_start();
    include '../../../payroll/template/mortuary/mortuary_function.php';
    include 'mortuary_function3.php';
    
	$query = new query;
	$query1 = new Unusedquery;

	$fincon = new PDO('mysql:host=localhost:3307;dbname=ebs', 'ebs','itprog2013');
	$hrmscon = new PDO('mysql:host=localhost:3307;dbname=pis', 'pis','itprog2013'); 
	// $tkcon = new PDO('mysql:host=localhost;dbname=timekeeping', 'root','itprog2013');
?>

<?php if ($_GET['request'] == 'table') :
	$lstcount = 0;
?>
	<table id = "table" class="table table-striped table-hover"  style="border:1px solid #eee;">
		<thead>
			<th id = "thpdd" class="warning">No.</th>
			<th id = "thpdd" class="warning">Action</th>
			<th id = "thpdd" class="warning">Requisition Date</th>
			<th id = "thpdd" class="warning">Employee Name</th>
			<th id = "thpdd" class="warning">Section</th>
			<th id = "thpdd" class="warning">Deceased Person</th>
			<th id = "thpdd" class="warning">Relation</th>
		<!-- 	<th id = "thpdd" class="warning">Allocated Amount</th> -->
			<!-- <th id = "thpdd" class="warning">Action</th> -->
		</thead>

		<tbody>	
		<?php foreach ($query->mort_requisition($_GET['ded_id']) as $details) : $lstcount++;?>
			<tr>
				<td><?php echo $lstcount; ?></td>
				<td onclick="viewRequisition(<?php echo $details['requisition_id']; ?>)"><center><i class="glyphicon glyphicon-tasks"></i></center></td>
				<td onclick="viewRequisition(<?php echo $details['requisition_id']; ?>)"><?php echo $details['requisition_date']; ?></td>
				<td onclick="viewRequisition(<?php echo $details['requisition_id']; ?>)"><?php echo utf8_encode($details['name']); ?></td>
				<td onclick="viewRequisition(<?php echo $details['requisition_id']; ?>)"><?php echo utf8_encode($details['emp_deptsec']); ?></td>
				<td onclick="viewRequisition(<?php echo $details['requisition_id']; ?>)"><?php echo utf8_encode($details['deceased_person']); ?></td>
				<td onclick="viewRequisition(<?php echo $details['requisition_id']; ?>)"><?php echo $details['relation']; ?></td>
				<!-- <td onclick="viewRequisition(<?php echo $details['requisition_id']; ?>)"><?php echo $details['relation']; ?></td> -->
				<!-- <td><a href="javascript:void" class="req_delete" id="<?php echo $details['requisition_id']; ?>"><button class="btn btn-round btn-success">Delete</button></a></td> -->
			</tr>
		<?php endforeach; ?>
		<?php if($lstcount == 0): ?>
			<tr>
				<td colspan="6"><center>No Data Available.</center></td>
			</tr>
		<?php endif;?>
		</tbody>
	</table>



	


	
	<?php elseif ($_GET['request'] == 'queryemp'):
		$key = $_GET['name'];
		$x = 0;
	?>
	
	<ul class='result'>
	<?php 	foreach ($query->query_emp($key) as $details):
				$tmp = array(
					'emp_id' => $details['emp_id'],
					'name' => utf8_encode($details['name']),
					'gender' => utf8_encode($details['gender']),
					'civilstatus' => utf8_encode($details['civilstatus']),
					'spouse' => utf8_encode($details['spouse']),
					'father' => utf8_encode($details['father']),
					'mother' => utf8_encode($details['mother']),
					'company_code' => $details['company_code'],
					'bunit_code' => $details['bunit_code'],
					'dept_code' => $details['dept_code'],
					'section_code' => $details['section_code']
				);
				$x += 1;
				
				echo "<li id='".$x."' class='test inactive'><a id='".$x."' class='opt' href='javascript:void' onclick='setEmpDetails(".json_encode($tmp).")'>".$tmp['name']."</a></li>";
			endforeach;
			if ($x == 0) {
				echo "<li><a><i>No Result Found.</i></a></li>";
			}
	?>
	</ul>

<?php elseif ($_GET['request'] == 'section') :
	$code[0] = explode("|", $_GET['cu'])[0];
	$code[1] = explode("|", $_GET['bu'])[0];
	$code[2] = explode("|", $_GET['dc'])[0];
	$code[3] = explode("|", $_GET['sc'])[0];
	$tbl[0] = explode("|", $_GET['cu'])[1];
	$tbl[1] = explode("|", $_GET['bu'])[1];
	$tbl[2] = explode("|", $_GET['dc'])[1];
	$tbl[3] = explode("|", $_GET['sc'])[1];
	$detaied_sec = "";
	$deli = "";
	for ($i=0; $i < count($code); $i++) { 	
		foreach ($query->query_section($tbl[$i], $code[$i]) as $details) :
			$tmpname = "";
			if ($detaied_sec != "" && $details[1] != "") {
				$deli = " -> ";
			}
			if (array_key_exists('acroname', $details)) {
				if ($details['acroname'] != "") {
					$tmpname = $details['acroname'];
				}
				else
				{
					$tmpname = $details[1];
				}
			}
			else {
				$tmpname = $details[1];
			}
			$detaied_sec = $detaied_sec.$deli.$tmpname;
		endforeach;
	}
	echo $detaied_sec;
?>

<?php elseif ($_GET['request'] == 'relation') :
	$name = $_GET['name'];
	$gender = $_GET['gender'];
	$status = $_GET['status'];
	$spouse_name = $_GET['spouse'];
	$father = $_GET['father'];
	$mother = $_GET['mother'];
	$spouse = "";
	if (strtoupper($gender) == "MALE") {
		echo "<option id='HIMSELF' value='$name'>HIMSELF</option>";
		$spouse = "WIFE";
	}
	else
	{
		echo "<option id='HERSELF' value='$name'>HERSELF</option>";
		$spouse = "HUSBAND";
	}
	echo "<option id='FATHER' value='$father'>FATHER</option>";
	echo "<option id='MOTHER' value='$mother'>MOTHER</option>";
	// if (strtoupper($status) == "MARRIED") {
		echo "<option id='".$spouse."' value='$spouse_name'>".$spouse."</option>";
		echo "<option id='SON' value=''>SON</option>";
		echo "<option id='DAUGHTER' value=''>DAUGHTER</option>";
	// }

?>

<?php elseif($_GET['request'] == 'save'):
	$datetag = date("Y-m-d") ."=".date('h-i-sA'); 
	$in = 0;
	$dir_arr = array("END", "DC", "BC", "MC", "EC", "AFF","BARCLR","CERTLET");
	$outdir_arr = array("","","","","","","","");
	if(is_array($_FILES)) {
		foreach ($_FILES['copyCert']['name'] as $name => $value){
			if(is_uploaded_file($_FILES['copyCert']['tmp_name'][$name])) {
				$fname_tmp =  explode(".", $_FILES["copyCert"]["name"][$name]);
				$ext = $fname_tmp[count($fname_tmp)-1];
				$target_dir = "../../../Shared Folder/MortuaryAttachment/".$dir_arr[$in]."/";
				// $target_file = $target_dir . basename($_FILES["copyCert"]["name"][$name]);
				$target_file = $target_dir . $datetag . "=" . $_GET['empid'] . "=" . $_GET['relation']. "." . $ext;
				$sourcePath = $_FILES['copyCert']['tmp_name'][$name];
				if(move_uploaded_file($sourcePath, $target_file)) {
					$outdir_arr[$in] = $target_file;
				}
				else 
				{
					$outdir_arr[$in] = "N/A";
				}
			}
			else
			{
				$outdir_arr[$in] = "NO DATA";
			}
			$in++;	
		}

		echo  $query->saveRequisition($_GET['empid'],stripslashes($_GET['section']), $_GET['relation'], $_GET['dateofdeath'], $_GET['deceased'], $outdir_arr[0], $outdir_arr[1], $outdir_arr[2], $outdir_arr[3], $outdir_arr[4], $outdir_arr[5], $outdir_arr[6],$outdir_arr[7]);

		echo  $query->saveRequisition_cebu($_GET['empid'],stripslashes($_GET['section']), $_GET['relation'], $_GET['dateofdeath'], $_GET['deceased'], $outdir_arr[0], $outdir_arr[1], $outdir_arr[2], $outdir_arr[3], $outdir_arr[4], $outdir_arr[5], $outdir_arr[6],$outdir_arr[7]);


	}
	else
	{
		//var_dump($_FILESs);
	}
?>



<?php elseif($_GET['request'] == 'saveAuth'):
	$datetag = date("Y-m-d") ."=".date('h-i-sA'); 
	$in = 0;
	$fname_tmp =  $_FILES['copyCert']['name'];
	$ext = $fname_tmp;
	$target_dir = "../../../Shared Folder/MortuaryAttachment/auth/";
	$target_file = $target_dir . $datetag . "=" . $_GET['empid'] . $ext;
	$sourcePath = $_FILES['copyCert']['tmp_name'];
	if(move_uploaded_file($sourcePath, $target_file)){
		$outdir_arr = $target_file;
	}
	

	 echo $query1->saveRequisition1($_GET['empid'], $_GET['relation'],$_SESSION['emp_id'] , $outdir_arr);

?>

<?php elseif($_GET['request'] == 'isauthor'):

	$emp_id = $_GET['emp_id'];
	
	 $sth = $fincon->prepare("SELECT *FROM mortuary_auth_ded where emp_id = '$emp_id' and  ded_type = 'Mortuary Fund Deduction waiver' ");
            $sth->execute();
            $row_count = $sth->fetch(PDO::FETCH_ASSOC);
            if($row_count['emp_id']>1){
            	echo TRUE;
            	   // echo $row_count['emp_id'];
            }else{
            	echo FALSE;
            }

?>


<?php elseif($_GET['request'] == 'isdone'):

     $person = $_GET['relation'];
     // echo $relation;
	 $sth = $fincon->prepare("SELECT *FROM mortuary_requisition where deceased_person = '$person' ");
            $sth->execute();
            $row_count = $sth->fetch(PDO::FETCH_ASSOC);
            if($row_count['emp_id']>1){
            	echo(true);
            }else{
            	echo(false);
            }

?>



<?php elseif($_GET['request'] == 'change_stat'):
	$id_p  		= $_POST['id_p'];
	$new_type_p = $_POST['new_type_p'];

	$sth = $fincon->prepare("UPDATE mortuary_auth_ded SET `ded_type` = '$new_type_p' WHERE `id` = '$id_p'");
        $sth->execute();  


?>

<?php elseif($_GET['request'] == 'update_employee_stat'):

	 $sth = $tkcon->prepare("SELECT  *FROM 1stcutoff where bioMetricId = '0000007000028' ");
     $sth->execute();
     while($row_count = $sth->fetch(PDO::FETCH_ASSOC)){
     	  $daysWork = $row_count['daysWork'];
     	  echo $daysWork;
     	  if($daysWork < 5){
     	  	echo "wala na";
     	  }
     	  else{
     	  	echo "naa pa";
     	  }
     }
?>



<?php elseif($_GET['request'] == 'search_emp'):
      $emp_name_p = $_POST['emp_name_p'];
      $x=1;
      $sth = $hrmscon->prepare("SELECT *FROM employee3 where name like '%$emp_name_p%' limit 40 ");
                          $sth->execute();
                          while($row_count = $sth->fetch(PDO::FETCH_ASSOC)){
                           // $emp_id = $row_count['emp_id'];
                           // $requisition_date = $row_count['requisition_date'];
                           // $added_by = $row_count['added_by'];
                           //     $sth1 = $hrmscon->prepare("SELECT *FROM employee3 where emp_id = '$emp_id' ");
                           //     $sth1->execute();
                           //     $row_count1 = $sth1->fetch(PDO::FETCH_ASSOC);
                           //     $dept =  $row_count1['company_code'].$row_count1['bunit_code'].$row_count1['dept_code'];

                           //     $dept = $hrmscon->prepare("SELECT *FROM locate_department where dcode = '$dept' ");
                           //     $dept->execute();
                           //     $row_countDept = $dept->fetch(PDO::FETCH_ASSOC);


                           //     $sth2 = $hrmscon->prepare("SELECT *FROM employee3 where emp_id = '$added_by' ");
                           //     $sth2->execute();
                           //     $row_count2 = $sth2->fetch(PDO::FETCH_ASSOC);
                           //     $pic = str_replace('../', '', $row_count['photo']);
                           //     $pic1 = '../'.$pic;
                           echo '<tr>
                                      <td>'.$x++.'</td>
                                      <td>'.$row_count['name'].'</td>
                                      <td>dd</td>
                                      <td>dd</td>
                                      <td>dd</td>
                                      <td>dd</td>
                                      <td>dd</td>
                                      <td>dd</td>
                                 </tr>';
                           }
?>


<?php elseif($_GET['request'] == 'search_emp2'):
    $emp_name_p = $_POST['emp_name_p'];

      $sth = $hrmscon->prepare("SELECT *FROM employee3 where name like '%$emp_name_p%' limit 10 ");
                          $sth->execute();
	                      while($row_count = $sth->fetch(PDO::FETCH_ASSOC)){

			                       echo '<a href="#">'.$row_count['name'].'</a><br>';
	                           }
?>


<?php elseif($_GET['request'] == 'delete_request'):
    $id = $_GET['id'];
    
    $id_p = $_POST['id_p'];
	$sth = $fincon->prepare("DELETE FROM mortuary_requisition where requisition_id = '$id' ");
    $sth->execute();  
?>

<?php elseif($_GET['request'] == 'add_type'):
    $emp_id = $_GET['id'];

     $sth = $fincon->prepare("SELECT *FROM mortuary_auth_ded where emp_id = '$emp_id' ");
           $sth->execute();
           $row_count = $sth->fetch(PDO::FETCH_ASSOC);
           if($row_count['emp_id']>1){
           		 $det = '<script>
           		 			alert("This Employee is already in the list!");
           		 		
           		 			$("#myModal").modal("hide");
           		 		</script>';
           }else{
           		$det = '';	
           }
    
    $sth = $hrmscon->prepare("SELECT *FROM employee3 where emp_id = '$emp_id' ");
           $sth->execute();
           $row_count = $sth->fetch(PDO::FETCH_ASSOC);
     echo $det;       
     echo '<form id="upload" action="" enctype="multipart/form-data" method="POST">';
     echo 'Name:<input value="'.$row_count['emp_id'].'" class="form-control emp_id" type="text" disabled="true" >';
	 echo 'Name:<input value="'.$row_count['name'].'" class="form-control emp_name" type="text" disabled="true" >';
	 echo 'Type:<select class="form-control ded_type" id="sel1" accept="image/*">;
		    		<optgroup label="SELECT HERE"></optgroup>
		    		<option>Authority to deduct</option>
		    		<option>Mortuary Fund Deduction waiver</option>
  			</select>';
  	// echo 'Select File:<input type="file" accept="image/*" name="copyCert" class="form-control type_photo" class="affidavit" id="affidavit">';
    echo '</form>';      
 //    $id_p = $_POST['id_p'];
	// $sth = $fincon->prepare("DELETE FROM mortuary_requisition where requisition_id = '$id' ");
 //    $sth->execute();
?>


<?php elseif($_GET['request'] == 'save_type'):

	$emp_id_p 	= $_GET['empid'];
	// $emp_name_p = $_GET['emp_name_p'];
	$ded_type_p = $_GET['ded_type'];



	$datetag = date("Y-m-d") ."=".date('h-i-sA'); 
	$in = 0;
	$fname_tmp =  $_FILES['copyCert']['name'];
	$ext = $fname_tmp;
	$target_dir = "../../../Shared Folder/MortuaryAttachment/auth/";
	$target_file = $target_dir . $datetag . "=" . $emp_id_p . $ext;
	$sourcePath = $_FILES['copyCert']['tmp_name'];
	if(move_uploaded_file($sourcePath, $target_file)){
		$outdir_arr = $target_file;
	}

	$sth = $fincon->prepare("INSERT INTO mortuary_auth_ded(
                                           `requisition_date`,
                                           `emp_id`,
                                           `added_by`,
                                           `ded_type`,
                                           `photo`) 
                              VALUES (
                                            '".date("Y-m-d")."',
                                            '".$emp_id_p."',
                                            '".$_SESSION['emp_id']."',
                                            '".$ded_type_p."',
                                            '".$outdir_arr."'
                                             )");
                             $sth->execute();
    echo "Request Succesfully Added";
?>

<?php elseif($_GET['request'] == 'del_type'):
	$id_p = $_POST['id_p'];
	$sth = $fincon->prepare("DELETE FROM mortuary_auth_ded where id = '$id_p' ");
    $sth->execute();  
    echo "Employee Deleted!";
?>






<?php elseif ($_GET['request']=='searh_ex_emp'):
 	 $emp_name = $_GET['emp_name'];
 	 $sth = $hrmscon->prepare("SELECT *FROM employee3 where name like '%$emp_name%' limit 6 ");
            $sth->execute();
            while($row_count = $sth->fetch(PDO::FETCH_ASSOC)){
            	if($row_count>0){
                   echo '<a class="emp_click" href="#" id="'.$row_count['emp_id'].'" id-name="'.$row_count['name'].'">'.$row_count['name'].'</a><br><br>';
            	}
            	else{

            		echo "No Employee Found";

            	}
            }
?>


<?php elseif ($_GET['request']=='save_exeption'):
 	  $date           = date('m-d-y');
 	  $emp_id_txt	  = $_GET['emp_id_txt']; 
	  $reason         = $_GET['reason'];
	  $added_by       = $_SESSION['emp_id'];

	  $sth = $fincon->prepare("INSERT INTO mortuary_except_ded(
                                           `emp_id`,
                                           `date_added`,
                                           `reason`,
                                           `added_by`
                                           ) 
                              VALUES (
                                            '".$emp_id_txt."',
                                            '".$date ."',
                                            '".$reason ."',
                                            '".$added_by."'
                                             )");
                             $sth->execute();


?>


<?php elseif ($_GET['request']=='delete_exeption'):
	$emp_id = $_GET['emp_id'];
	$sth = $fincon->prepare("DELETE FROM mortuary_except_ded where id = '$emp_id' ");
    $sth->execute();  
    echo "Employee Deleted from List";
?>


<?php elseif ($_GET['request']=='table1'):

	echo '<table id = "table" class="table table-striped table-hover"  style="border:1px solid #eee;">
		<thead>
			<th id = "thpdd" class="warning">No.</th>
			<th id = "thpdd" class="warning">Action</th>
			<th id = "thpdd" class="warning">Requisition Date</th>
			<th id = "thpdd" class="warning">Employee Name</th>
			<th id = "thpdd" class="warning">Section</th>
			<th id = "thpdd" class="warning">Deceased Person</th>
			<th id = "thpdd" class="warning">Relation</th>
			<th id = "thpdd" class="warning">Action</th>
		</thead>
	<tbody>';

	$name = $_GET['name'];
	$lstcount = 1;
	$sth = $fincon->prepare("SELECT *FROM mortuary_requisition where deceased_person like '%$name%' limit 10 ");
	$sth->execute();
	while($row_count = $sth->fetch(PDO::FETCH_ASSOC)){
		$emp_id = $row_count['emp_id'];
		
		$sth1 = $hrmscon1->prepare("SELECT emp_id,name FROM employee3 where emp_id = '$emp_id' ");
		$sth1->execute();
		$row_count1 = $sth1->fetch(PDO::FETCH_ASSOC);

		echo '<tr>
				<td>'.$lstcount++.'</td>
				<td onclick="viewRequisition('.$row_count['requisition_id'].')"><center><i class="glyphicon glyphicon-tasks"></i></center></td>
				<td onclick="viewRequisition('.$row_count['requisition_id'].')">'.$row_count['requisition_date'].'</td>
				<td onclick="viewRequisition('.$row_count['requisition_id'].')">'.utf8_encode($row_count1['name']).'</td>
				<td onclick="viewRequisition('.$row_count['requisition_id'].')">'.utf8_encode($row_count['emp_deptsec']).'</td>
				<td onclick="viewRequisition('.$row_count['requisition_id'].')">'.utf8_encode($row_count['deceased_person']).'</td>
				<td onclick="viewRequisition('.$row_count['requisition_id'].')">'.$row_count['relation'].'</td>
				<td><a href="javascript:void" class="req_delete" id="'.$row_count['requisition_id'].'"><button class="btn btn-round btn-success">Delete</button></a></td>
			</tr>'; 
	}
	echo '</tbody>
	</table>';

?>


<?php endif;?>

